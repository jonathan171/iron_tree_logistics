<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use App\Entity\UserCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends ServiceEntityRepository<UserCompany>
 *
 * @method UserCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCompany[]    findAll()
 * @method UserCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCompanyRepository extends ServiceEntityRepository
{   
    protected $traslator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $traslator)
    {   
        $this->traslator = $traslator;
        parent::__construct($registry, UserCompany::class);
    }

    public function save(UserCompany $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCompany $entity, bool $flush = false): void
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

        $query = $this->createQueryBuilder('f')
                    ->innerJoin(User::class, 'u',Join::WITH, 'f.user = u.id')
                    ->innerJoin(Company::class,'c',Join::WITH,'f.company = c.id');
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('f.discount like :val  OR u.first_name like :val  OR u.last_name like :val OR c.name like :val')
                ->setParameter('val', $shearch);
        }

        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];


        foreach ($paginator as $item) {

            $actions = '<a  class="btn waves-effect waves-light btn-info" href="/user_company/' . $item->getId() . '/edit">'. $this->traslator->trans('labels.edit') .'</a>';



            $list[] = [
                'user' => $item->getUser()->getFirstName().' '.$item->getUser()->getLastName(),
                'company' => $item->getCompany()->getName(),
                'discount' => $item->getDiscount(),
                'actions' => $actions
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }

    //    public function findOneBySomeField($value): ?UserCompany
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
