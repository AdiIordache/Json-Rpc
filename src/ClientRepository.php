<?php


namespace Adi\JsonRpc;
use Doctrine\ORM\EntityRepository;



class ClientRepository  extends EntityRepository
{

    public function getInvoices($filters): Invoice
    {
        $query = $this->createQueryBuilder('sub');

        foreach ($filters as $key => $value) {
            $condition = (is_array($value) && !empty($value)) ? "IN (:{$key})" : "= :{$key}";

            switch ($key) {
                case 'id':
                    $query->andWhere("sub.{$key} {$condition}")
                        ->setParameter($key, $value);
            }
            return $query->getResults();
        }
    }
}