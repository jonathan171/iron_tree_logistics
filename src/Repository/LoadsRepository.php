<?php

namespace App\Repository;

use App\Entity\Loads;
use App\Entity\Trier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends ServiceEntityRepository<Loads>
 *
 * @method Loads|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loads|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loads[]    findAll()
 * @method Loads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoadsRepository extends ServiceEntityRepository
{   
    protected $manager;
    protected $traslator;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager, TranslatorInterface $traslator)
    {

        $this->manager = $manager;
        $this->traslator = $traslator;
        parent::__construct($registry, Loads::class);

    }
    

    public function save(Loads $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Loads $entity, bool $flush = false): void
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

        $query = $this->createQueryBuilder('l')
        ->leftJoin(Trier::class, 't', Join::WITH,   't.id = l.trier')
        ->where('l.company = :company')
            ->setParameter('company',$options['company']);
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('l.code like :val  OR l.driver_name like :val  OR l.arrived_at_loader like :val OR t.description like :val OR t.percentage like :val')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
       
        $triers = $this->manager->getRepository(Trier::class)->findAll();
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/loads/'.$item->getId().'/edit">'. $this->traslator->trans('labels.edit') .'</a>';

            $actions.='<a class="icon-select"  style="position:relative; float:right;cursor:pointer;" onclick="deductions('.$item->getId().');"  title="deductions">
            <i class="fa fa-eye text-success" ></i>
             </a>';

            $select ='<select class="form-select" data-id="'.$item->getId().'">';
            $select .='<option value= ""> </option>';

            foreach ($triers as $trier){
                if($trier == $item->getTrier()){
                    $select .='<option value= "'.$trier->getId().'" selected="selected">'.$trier->getDescription().' - '.$trier->getPercentage().' %</option>';

                }else{
                    $select .='<option value= "'.$trier->getId().'">'.$trier->getDescription().' - '.$trier->getPercentage().' %</option>';
                }

            }
            $select .='</select>';
           
            $list[] = ['code'=>$item->getCode(),
                       'driverName'=>$item->getDriverName(),
                       'arrivedAtLoader'=>$item->getArrivedAtLoader()->format('Y-m-d H:i:s'),
                       'tier' => $select,
                       'actions'=>$actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

//    public function findOneBySomeField($value): ?Loads
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
