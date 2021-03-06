<?php
namespace HashReviews\Transaction\ReviewBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Review
{

    // custom methods

    public function getAuthorAddress() {
        if($this->getRe() == "to")
            return $this->getFromAddress();
        elseif($this->getRe() == "from")
            return $this->getToAddress();
        else 
            return false;

    }

    public function getReAddress() {
        if($this->getRe() == "to")
            return $this->getToAddress();
        elseif($this->getRe() == "from")
            return $this->getFromAddress();
        else 
            return false;

    }

    public function getReadUrl($controller) {

        return $controller->get('router')->generate('read', array(
            're' => $this->getRe(),
            'tx_hash' => $this->getTxHash(),
            'from_address' => $this->getFromAddress(),
            'to_address' => $this->getToAddress()

        ));
    }



    // end custom methods

    /**
     * @var string
     */
    private $tx_hash;

    /**
     * @var string
     */
    private $from_address;

    /**
     * @var string
     */
    private $to_address;

    /**
     * @var string
     */
    private $re;

    /**
     * @var \DateTime
     */
    private $tx_date;

    /**
     * @var string
     */
    private $tx_amount;

    /**
     * @var integer
     */
    private $rating;

    /**
     * @var string
     */
    private $review;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $review_message;

    /**
     * @var string
     */
    private $random;

    /**
     * @var string
     */
    private $review_message_hash;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;


    /**
     * Set tx_hash
     *
     * @param string $txHash
     * @return Review
     */
    public function setTxHash($txHash)
    {
        $this->tx_hash = $txHash;

        return $this;
    }

    /**
     * Get tx_hash
     *
     * @return string 
     */
    public function getTxHash()
    {
        return $this->tx_hash;
    }

    /**
     * Set from_address
     *
     * @param string $fromAddress
     * @return Review
     */
    public function setFromAddress($fromAddress)
    {
        $this->from_address = $fromAddress;

        return $this;
    }

    /**
     * Get from_address
     *
     * @return string 
     */
    public function getFromAddress()
    {
        return $this->from_address;
    }

    /**
     * Set to_address
     *
     * @param string $toAddress
     * @return Review
     */
    public function setToAddress($toAddress)
    {
        $this->to_address = $toAddress;

        return $this;
    }

    /**
     * Get to_address
     *
     * @return string 
     */
    public function getToAddress()
    {
        return $this->to_address;
    }

    /**
     * Set re
     *
     * @param string $re
     * @return Review
     */
    public function setRe($re)
    {
        $this->re = $re;

        return $this;
    }

    /**
     * Get re
     *
     * @return string 
     */
    public function getRe()
    {
        return $this->re;
    }

    /**
     * Set tx_date
     *
     * @param \DateTime $txDate
     * @return Review
     */
    public function setTxDate($txDate)
    {
        $this->tx_date = $txDate;

        return $this;
    }

    /**
     * Get tx_date
     *
     * @return \DateTime 
     */
    public function getTxDate()
    {
        return $this->tx_date;
    }

    /**
     * Set tx_amount
     *
     * @param string $txAmount
     * @return Review
     */
    public function setTxAmount($txAmount)
    {
        $this->tx_amount = $txAmount;

        return $this;
    }

    /**
     * Get tx_amount
     *
     * @return string 
     */
    public function getTxAmount()
    {
        return $this->tx_amount;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set review
     *
     * @param string $review
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Review
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set review_message
     *
     * @param string $reviewMessage
     * @return Review
     */
    public function setReviewMessage($reviewMessage)
    {
        $this->review_message = $reviewMessage;

        return $this;
    }

    /**
     * Get review_message
     *
     * @return string 
     */
    public function getReviewMessage()
    {
        return $this->review_message;
    }

    /**
     * Set random
     *
     * @param string $random
     * @return Review
     */
    public function setRandom($random)
    {
        $this->random = $random;

        return $this;
    }

    /**
     * Get random
     *
     * @return string 
     */
    public function getRandom()
    {
        return $this->random;
    }

    /**
     * Set review_message_hash
     *
     * @param string $reviewMessageHash
     * @return Review
     */
    public function setReviewMessageHash($reviewMessageHash)
    {
        $this->review_message_hash = $reviewMessageHash;

        return $this;
    }

    /**
     * Get review_message_hash
     *
     * @return string 
     */
    public function getReviewMessageHash()
    {
        return $this->review_message_hash;
    }

    /**
     * Set signature
     *
     * @param string $signature
     * @return Review
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string 
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Review
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Review
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Review
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    /**
     * @var string
     */
    private $sign_link;


    /**
     * Set sign_link
     *
     * @param string $signLink
     * @return Review
     */
    public function setSignLink($signLink)
    {
        $this->sign_link = $signLink;

        return $this;
    }

    /**
     * Get sign_link
     *
     * @return string 
     */
    public function getSignLink()
    {
        return $this->sign_link;
    }
}
