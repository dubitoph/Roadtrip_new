<?php
namespace App\Repository\advert;

use App\Entity\advert\Advert;
use App\Entity\advert\AdvertSearch;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advert::class);
    }
    public function findSearchedAdverts(AdvertSearch $search) : array
    {
        $query = $this->createQueryBuilder('a')
                      ->addSelect('MIN(p.price / d.daysNumber) as minPrice')
                      ->leftJoin('a.vehicle', 'v')
                      ->leftJoin('a.prices', 'p')
                      ->leftJoin('p.duration', 'd')
                      ->andWhere('a.expiresAt >= :today')
                      ->groupBy('a.id')
                      ->setParameter('today', date("Y-m-d"))
        ;
        if ($search->getLatitude() && $search->getLongitude()) 
        {
            $selectSqlDistance = '6353 * 2 * ASIN(SQRT( POWER(SIN((s.latitude - abs(:latitude)) * pi()/180 / 2),2) + COS(s.latitude * pi()/180 ) * ' . 
                                 'COS(abs(:latitude) *  pi()/180) * POWER(SIN((s.longitude - :longitude) *  pi()/180 / 2), 2) )) as distance';
            $query
                ->addSelect($selectSqlDistance)
                ->setParameter('longitude', $search->getLongitude())
                ->setParameter('latitude', $search->getLatitude())
            ;
            if (! $search->getDistance()) 
            {                
                $query->leftJoin('v.situation', 's');
            
            }
            if ($search->getDistance()) 
            {
                $whereSqlDistance = '(6353 * 2 * ASIN(SQRT( POWER(SIN((s.latitude - abs(:latitude)) * pi()/180 / 2),2) + COS(s.latitude * pi()/180 ) * ' . 
                                    'COS(abs(:latitude) *  pi()/180) * POWER(SIN((s.longitude - :longitude) *  pi()/180 / 2), 2) )))';
                $query
                      ->join('v.situation', 's')
                      ->andWhere($whereSqlDistance . '<= :distance')
                      ->setParameter('distance', $search->getDistance());
            }
            if (! $search->getSorting() || $search->getSorting() === 'Proximité' || $search->getSorting() === 'Proximité + prix') 
            {
                $query->addOrderBy('distance', 'ASC');
 
            }
        }
        if ($search->getMinimumBedsNumber())
        {
            $query = $query->andWhere('v.bedsNumber >= :minBedsNumber')
                           ->setParameter('minBedsNumber', $search->getMinimumBedsNumber())
            ;
        }
        $equipments = new ArrayCollection(array_merge($search->getCellEquipments()->toArray(), $search->getCarrierEquipments()->toArray()));
        
        if ($equipments->count() > 0)
        {
            $k = 0;
            foreach ($equipments as $equipment) 
            {
                $k++;
                $query = $query->andWhere(":equipment$k MEMBER OF v.equipments")
                               ->setParameter("equipment$k", $equipment)
                ;
            }
        }
        if ($search->getMaximumPrice()) 
        {
            $query = $query
                        ->andWhere('p.price / d.daysNumber <= :maxPrice')
                        ->setParameter('maxPrice', $search->getMaximumPrice())
            ;
        }
        if (! $search->getSorting() || $search->getSorting() === 'Prix' || $search->getSorting() === 'Proximité + prix') 
        {
            $query->addOrderBy('minPrice', 'ASC');
        }

        return $query->getQuery()
                     ->getResult();
    }

    public function lasAdverts(): array
    {
        return $this->createQueryBuilder('a')
                    ->addSelect('MIN(p.price / d.daysNumber) as minPrice')
                    ->leftJoin('a.prices', 'p')
                    ->leftJoin('p.duration', 'd')
                    ->andWhere('a.expiresAt IS NOT NULL')
                    ->andWhere('a.expiresAt >= :today')
                    ->groupBy('a.createdAt')
                    ->orderBy('a.createdAt', 'DESC')
                    ->setParameter('today', date("Y-m-d"))
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult()
        ;
    }

    public function favorites($adverts, $latitude = null, $longitude = null): array
    {

        $query = $this->createQueryBuilder('a')
                      ->addSelect('MIN(p.price / d.daysNumber) as minPrice')
        ;

        if ($latitude && $longitude) 
        {                   
        
            $selectSqlDistance = '6353 * 2 * ASIN(SQRT( POWER(SIN((s.latitude - abs(:latitude)) * pi()/180 / 2),2) + COS(s.latitude * pi()/180 ) * ' . 
            'COS(abs(:latitude) *  pi()/180) * POWER(SIN((s.longitude - :longitude) *  pi()/180 / 2), 2) )) as distance';

            $query->addSelect($selectSqlDistance)
                  ->setParameter('latitude', $latitude)
                  ->setParameter('longitude', $longitude)
            ;

        }  
                     
        $query->leftJoin('a.vehicle', 'v')
              ->leftJoin('v.situation', 's')
              ->leftJoin('a.prices', 'p')
              ->leftJoin('p.duration', 'd')
              ->andWhere('a.expiresAt >= :today')
              ->andWhere('a in (:adverts)')
              ->groupBy('a.id')
              ->setParameter('today', date("Y-m-d"))
              ->setParameter('adverts', $adverts)
        ;
        
        return $query->getQuery()
                     ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}