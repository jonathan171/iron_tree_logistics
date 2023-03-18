<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDocument>
 *
 * @method UserDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDocument[]    findAll()
 * @method UserDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDocument::class);
    }

    public function save(UserDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserDocument $entity, bool $flush = false): void
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

        $query = $this->createQueryBuilder('ud')
        ->leftJoin(User::class, 'u', Join::WITH,   'u.id = ud.user');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('u.first_name like :val OR u.last_name like :val')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
       
        
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-warning" href="/user_document/'.$item->getId().'/edit">editar</a>';
            $actions.= '<a  class="btn waves-effect waves-light btn-info" href="/user_document/'.$item->getId().'/show">Ver</a>';
           
            
           
            $list[] = ['user'=>$item->getUser()->getFirstName().''.$item->getUser()->getLastName(),
                       'actions'=>$actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

//    public function findOneBySomeField($value): ?UserDocument
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
