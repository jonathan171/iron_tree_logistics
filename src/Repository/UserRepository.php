<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{   
    protected $traslator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $traslator)
    {   
        $this->traslator = $traslator;
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function findByDataTable(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;
        $campos = array(
            'email',
            'first_name',
            'last_name'
        );

        $query = $this->createQueryBuilder('u');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('u.email like :val OR u.first_name like :val2 OR u.last_name like :val3 ')
            ->setParameters(['val'=>$shearch,'val2'=>$shearch,'val3'=>$shearch]);
        }
        $query->orderBy("u.{$campos[$options['order']['column']]}" ,$options['order']['dir']);
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/user/'.$item->getId().'/edit">'. $this->traslator->trans('labels.edit') .'</a>';
           
            $list[] = ['email'=>$item->getEmail(),
                       'nombres'=>$item->getFirstName(),
                       'apellidos'=>$item->getLastName(),
                       'actions'=> $actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

    public function findByDataShearch(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('u');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->where('u.first_name like :val  OR u.last_name like :val OR u.email like :val')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

           
           

            
            $list[] = [
                'id' => $item->getId(),       
                'text'=>$item->getFirstName().' '. $item->getLastName()
            ];
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
