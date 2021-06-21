<?php


namespace Adi\JsonRpc;


class CLient
{
    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Invoice
     * @ORM\OneToMany (targetEntity=Invoice::class, ")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned
     */
    protected $invoices;

    /**
     * @return Invoice
     */
    public function getInvoices(): Invoice
    {
        return $this->invoices;
    }

    /**
     * @param Invoice $invoices
     */
    public function setInvoices(Invoice $invoices): void
    {
        $this->invoices = $invoices;
    }

}
