<?php


namespace Adi\JsonRpc;


class Invoice
{
    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    /**
     * @param Invoice $invoice
     */
    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    /**
     * @var Invoice
     * @ORM\ManyToOne(targetEntity=Client::class, ")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned
     */
    protected $invoice;



}
