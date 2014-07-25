<?php

namespace HashReviews\Transaction\ReviewBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction
{
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
     * @var \DateTime
     */
    private $tx_date;

    /**
     * @var string
     */
    private $tx_amount;

    /**
     * @var string
     */
    private $request_hash;

    /**
     * @var string
     */
    private $created_at;

    /**
     * @var string
     */
    private $updated_at;


    /**
     * Set tx_hash
     *
     * @param string $txHash
     * @return Transaction
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
     * @return Transaction
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
     * @return Transaction
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
     * Set tx_date
     *
     * @param \DateTime $txDate
     * @return Transaction
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
     * @return Transaction
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
     * Set request_hash
     *
     * @param string $requestHash
     * @return Transaction
     */
    public function setRequestHash($requestHash)
    {
        $this->request_hash = $requestHash;

        return $this;
    }

    /**
     * Get request_hash
     *
     * @return string 
     */
    public function getRequestHash()
    {
        return $this->request_hash;
    }

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return Transaction
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return string 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * @return Transaction
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return string 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}
