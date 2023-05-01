<?php

namespace App\Repository;

use App\Entity\FuelTransactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends ServiceEntityRepository<FuelTransactions>
 *
 * @method FuelTransactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method FuelTransactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method FuelTransactions[]    findAll()
 * @method FuelTransactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FuelTransactionsRepository extends ServiceEntityRepository
{   
    protected $traslator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $traslator)
    {   
        $this->traslator = $traslator;
        parent::__construct($registry, FuelTransactions::class);
    }

    public function save(FuelTransactions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FuelTransactions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDataTable(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('f');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('f.code like :val  OR f.yard_name like :val  OR f.date like :val ')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
       
        
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/fuel_transactions/'.$item->getId().'/edit">'. $this->traslator->trans('labels.edit') .'</a>';

           
           
            $list[] = ['code'=>$item->getCode(),
                       'yard_name'=>$item->getYardName(),
                       'date'=>$item->getDate()->format('Y-m-d'),
                       'actions'=>$actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

//    public function findOneBySomeField($value): ?FuelTransactions
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
