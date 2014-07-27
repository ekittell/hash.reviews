<?php

namespace HashReviews\Transaction\ReviewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HashReviews\Transaction\ReviewBundle\Entity\Review;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{

	private function rpcRequest($method, $params) {
		$request = array(
						'method' => $method,
						'params' => $params,
						'id' => 1
						);
		$request = json_encode($request); 

    	$rpc_user = $this->container->getParameter('rpc_user');
		$rpc_pass = $this->container->getParameter('rpc_pass');
		$rpc_host = $this->container->getParameter('rpc_host');
		$rpc_port = $this->container->getParameter('rpc_port');


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	    curl_setopt($ch, CURLOPT_URL, $rpc_user.':'.$rpc_pass.'@'.$rpc_host.':'.$rpc_port);
	    //curl_setopt($ch, CURLOPT_SSLVERSION, 3); 
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $request); 
	    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	    //curl_setopt($ch, CURLOPT_CAPATH, "/home/kittell/hash.reviews");
	    $response = curl_exec($ch);
	    if(curl_errno($ch))
		{
			$this->get('session')->getFlashBag()->add(
		            'error',
		            'error:' . curl_error($ch)
		        );
		}
	    curl_close($ch);
	    $response = json_decode($response, true);
	    return $response;
	}

	public function initAction($re, $tx_hash, $from_address, $to_address, $sign_link = null) 
	{
		$form_data = array(
					're' => $re,
					'tx_hash' => $tx_hash,
		    		'from_address' => $from_address,
		    		'to_address' => $to_address,
		    		'sign_link' => $sign_link
		    	);
		$new_request = Request::create(
		    '/index',
		    'POST',
		    array(	'form' => $form_data )
		);

		return $this->forward('HashReviewsTransactionReviewBundle:Default:index', array(
						    'request' => $new_request,
						    'init' => true
						));
	}

    public function indexAction(Request $request, $init = false)
    {
    	$review = new Review();
    	$form = $this->createFormBuilder($review)
	        ->add('re', 'choice', array(
        		'choices' => array(
        			"to" => "SENT",
        			"from" => "RECEIVED"
        		),
        		'data' => 'to',
        		'expanded' => true,
        		'label' => "This review is for a transaction where bitcoin was:",
        		'empty_value' => false))
            ->add('tx_hash', 'text', array('label' => 'Transaction Hash'))
            ->add('from_address', 'text', array('label' => 'From Address'))
            ->add('to_address', 'text', array('label' => 'To Address'))
            ->add('write_review', 'submit', array('label' => 'Write Review'))
            ->add('request_review', 'submit', array('label' => 'Request Review'))
            ->getForm();

	    $form->handleRequest($request);


	    if ($form->isValid() || $init) {
	    	$values = $request->request->get('form');


        	$session = new Session();
			$session->start();
        	$tx_data = $form->getData();

    		if ($fp = @fopen("http://blockchain.info/rawtx/" . $tx_data->getTxHash(), 'r')) {
				$response = '';
				while($row = fgets($fp)) {
					$response.= trim($row)."\n";
				}
				$response = json_decode($response,true);

				$total_input = 0;
				foreach($response['inputs'] as $input) {
					if($input['prev_out']['addr'] == $tx_data->getFromAddress())
						$confirm_from = true;
					$total_input += $input['prev_out']['value'];
				}

				$total_output = 0;
				foreach($response['out'] as $output) {
					if($output['addr'] == $tx_data->getToAddress())
						$confirm_to = true;
					$total_output += $output['value'];
				}

				if(isset($response['out'][1])) // not so sure about this logic yet, is [1] always your 'change'?
					$est_total = $total_input - $response['out'][1]['value'];
				else
					$est_total = $total_input;


				if($confirm_from && $confirm_to) {
					if(isset($values['request_review'])) {
						return $this->forward('HashReviewsTransactionReviewBundle:Default:request', array(
						    'data' => $tx_data
						));

					} else {
						// extra details from api
			        	$tx_time = new \DateTime();
			        	$tx_time->setTimestamp($response['time']);
			        	$tx_data->setTxDate($tx_time);

			        	if ($fp = @fopen("https://blockchain.info/q/txfee/" . $tx_data->getTxHash(), 'r'))
							$fee = stream_get_contents($fp);
						else
							$this->get('session')->getFlashBag()->add(
					            'error',
					            'Unable to connect to BlockChain.info.'
					        );

			        	$tx_data->setTxAmount(($est_total - $fee)/100000000);

			        	$session->set('tx_data', $tx_data);

			        	return $this->redirect($this->generateUrl('enter_review'));
					}


		        } else {
		        	$this->get('session')->getFlashBag()->add(
			            'error',
			            'Address(es) not found in transaction.'
			        );
		        }
			} else {
				$this->get('session')->getFlashBag()->add(
		            'error',
		            'We could not find that transaction.  If you\'re sure this hash exists please contact us immediately.  Thanks!'
		        );
			}


	    }

        return $this->render('HashReviewsTransactionReviewBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function reviewAction(Request $request)
    {
    	$session = $request->getSession();
    	$tx_data = $session->get('tx_data');

    	if(!$tx_data)
    		throw new \Exception('Transaction Data Not Found');

    	$review = $this->getDoctrine()
	        ->getRepository('HashReviewsTransactionReviewBundle:Review')
	        ->find(array(
	        	"re" => $tx_data->getRe(), 
	        	"tx_hash" => $tx_data->getTxHash(), 
	        	"from_address" => $tx_data->getFromAddress(), 
	        	"to_address" => $tx_data->getToAddress()
	        ));

	    $existing_link = false;
	    if( $review )
	    	$existing_link = $review->getReadUrl($this);
	    else
	    	$review = $tx_data;



	    $form = $this->createFormBuilder($review)
            ->add('rating', 'choice', array(
            		'choices' => array(1,2,3,4,5),
            		'expanded' => true))
            ->add('review', 'textarea')
            ->add('Continue', 'submit')
            ->getForm();

        $form->handleRequest($request);


	    if ($form->isValid()) {
	    	$bytes = openssl_random_pseudo_bytes(32);
    		$hex   = bin2hex($bytes);

    		$version = "1.0";

	        $review_message = "Version: " . $version . "\r\n";
	        $review_message .= "Hash: " . $review->getTxHash() . "\r\n";
	        $review_message .= "From: " . $review->getFromAddress() . "\r\n";
	        $review_message .= "To: " . $review->getToAddress() . "\r\n";
	        $review_message .= "Re: " . $review->getRe() . "\r\n";
	        $review_message .= "Rating: " . $form->getData()->getRating() . "\r\n";
	        $review_message .= "Review: " . $form->getData()->getReview() . "\r\n";
	        $review_message .= "Review Time: " . time() . "\r\n";
	        $review_message .= "Random: " . $hex . "\r\n";

	        $review_hash = hash("sha256", hash("sha256", $review_message));

	        $review->setRating($form->getData()->getRating());
	        $review->setReview($form->getData()->getReview());
	        $review->setVersion($version);
	        $review->setReviewMessage($review_message);
	        $review->setReviewMessageHash($review_hash);
	        $review->setRandom($hex);
	        $review->setStatus('pending');
	        $now = new \DateTime("now");
	        if($existing_link)
	        	$review->setUpdatedAt($now);
	        else
	        	$review->setCreatedAt($now);

	        $em = $this->getDoctrine()->getManager();
		    $em->persist($review);
		    $em->flush();



		    return $this->redirect($this->generateUrl('sign_hash'));

	        
	    }

        return $this->render('HashReviewsTransactionReviewBundle:Default:review.html.twig', array(
            'form' => $form->createView(),
            'existing_link' => $existing_link,
            'review' => $review
        ));

    }

    public function signAction(Request $request)
    {
    	$session = $request->getSession();
    	$tx_data = $session->get('tx_data');

    	if($tx_data) {
	    	$review = $this->getDoctrine()
		        ->getRepository('HashReviewsTransactionReviewBundle:Review')
		        ->find(array(
		        	"re" => $tx_data->getRe(), 
		        	"tx_hash" => $tx_data->getTxHash(), 
		        	"from_address" => $tx_data->getFromAddress(), 
		        	"to_address" => $tx_data->getToAddress()
		        ));
    	} elseif( $hash = $request->request->get('review_message_hash')) {
	    	$review = $this->getDoctrine()
		        ->getRepository('HashReviewsTransactionReviewBundle:Review')
		        ->findOneByReviewMessageHash($hash);
    	} else {
    		throw new \Exception('Transaction Not Found');
    	}

	    if( !$review )
	    	throw new \Exception('Review Not Found');


	   	$form = $this->createFormBuilder($review)
	   		->add('review_message_hash', 'text')
	   		->add('signature', 'text')
            ->add('Save', 'submit')
            ->getForm();

        $form->handleRequest($request);

	    if ($form->isValid()) { // verify signature with Bitcoind api
	    	$response = $this->rpcRequest("verifymessage", array($review->getAuthorAddress(), $form->getData()->getSignature(), $form->getData()->getReviewMessageHash()));
	    	if($response['result']) {

		        $review->setSignature($form->getData()->getSignature());
		        $review->setStatus('verified');

		        $em = $this->getDoctrine()->getManager();
			    $em->persist($review);
			    $em->flush();

			    return $this->forward('HashReviewsTransactionReviewBundle:Default:success', array(
				    'review'  => $review
				));
	    	}
	    	else {
	    		$this->get('session')->getFlashBag()->add(
		            'error',
		            'Signature verification failed.'
		        );
	    	}
	        
	    }

        return $this->render('HashReviewsTransactionReviewBundle:Default:sign.html.twig', array(
            'form' => $form->createView(),
            'review' => $review
        ));
    }

    public function successAction($review)
    {
    	$link = $review->getReadUrl($this);
        return $this->render('HashReviewsTransactionReviewBundle:Default:success.html.twig', array(
            'link' => $link,
            'review' => $review
        ));
    }

    public function readAction($re, $tx_hash, $from_address, $to_address)
    {
    	$review = $this->getDoctrine()
		        ->getRepository('HashReviewsTransactionReviewBundle:Review')
		        ->find(array(
		        	"re" => $re, 
		        	"tx_hash" => $tx_hash, 
		        	"from_address" => $from_address, 
		        	"to_address" => $to_address
		        ));

        return $this->render('HashReviewsTransactionReviewBundle:Default:read.html.twig', array(
            'review' => $review,
            'revise_link' => $this->getInitLink( $review )
        ));
    }

    public function reviewsAction( $search_query )
    {
    	$em = $this->getDoctrine()->getManager();

    	if(strlen($search_query) > 34) {
    		$is_tx = true;
			$query = $em->createQuery(
			    "SELECT r
			    FROM HashReviewsTransactionReviewBundle:Review r
			    WHERE r.tx_hash = :tx_hash
			    ORDER BY r.tx_date DESC"
			)->setParameter('tx_hash', $search_query);
    	} else {
    		$is_tx = false;
			$query = $em->createQuery(
			    "SELECT r
			    FROM HashReviewsTransactionReviewBundle:Review r
			    WHERE
			    (r.re = 'to' AND r.to_address = :to_address) OR
			    (r.re = 'from' AND r.from_address = :from_address)
			    ORDER BY r.created_at DESC"
			)->setParameter('to_address', $search_query)
			 ->setParameter('from_address', $search_query);
    	}

    	$reviews = $query->getResult();

    	if(!$reviews)
    		$this->get('session')->getFlashBag()->add(
		            'error',
		            'No reviews found.'
		        );

    	

        return $this->render('HashReviewsTransactionReviewBundle:Default:reviews.html.twig', array(
            'reviews' => $reviews,
            'query' => $search_query,
            'is_tx' => $is_tx
        ));

    }

    public function requestAction( $data ) {
    	$link = $this->getInitLink( $data );
    	return $this->render('HashReviewsTransactionReviewBundle:Default:request.html.twig', array(
            'link' => $link,
            'data' => $data
        ));
    }

    private function getInitLink( $data ) {
    	return $this->get('router')->generate('init', array(
            're' => $data->getRe(),
            'tx_hash' => $data->getTxHash(),
            'from_address' => $data->getFromAddress(),
            'to_address' => $data->getToAddress()

        ));
    }


}
