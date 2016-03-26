<?php
// src/ozyx/PlatformBundle/Entity/AdvertRepository.php

namespace ozyx\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AdvertRepository extends EntityRepository
{

  public function getPublishedQueryBuilder()
  {
    return $this
      ->createQueryBuilder('a')
      ->where('a.published = :published')
      ->setParameter('published', true)
    ;
  }

  public function getAdverts($page, $nbPerPage)
  {
    $query = $this->createQueryBuilder('a')
      // Jointure sur l'attribut image
      ->leftJoin('a.advertSkills', 's')
      ->where('s.advert = a.id')
      ->orWhere('s.advert IS NULL')
      ->addSelect('s')
      // Jointure sur l'attribut image
      ->leftJoin('a.image', 'i')
      ->addSelect('i')
      // Jointure sur l'attribut categories
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
      ->orderBy('a.date', 'DESC')
      ->getQuery()
    ;

    $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbPerPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbPerPage)
    ;

    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
  }
  
  public function whereCurrentYear(QueryBuilder $qb)
  {
    $qb
      ->andWhere('a.date BETWEEN :start AND :end')
      ->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
      ->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
    ;
  }

  public function getAdvertWithApplications()
  {
	$qb = $this
	  ->createQueryBuilder('a')
	  ->leftJoin('a.applications', 'app')
	  ->addSelect('app')
	;

    return $qb
	  ->getQuery()
	  ->getResult()
	;
  }

  public function getAdvertWithCategories(array $categoryNames)
  {
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->join('a.categories', 'c')
      ->addSelect('c')
    ;

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    $qb->where($qb->expr()->in('c.name', $categoryNames));
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getApplicationsWithAdvert($limit)
  {
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Advert avec pour alias « adv »
    $qb
      ->join('a.advert', 'adv')
      ->addSelect('adv')
    ;

    // Puis on ne retourne que $limit résultats
    $qb->setMaxResults($limit);

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
      ;
  }

  public function isFlood($ip, $secondes)
  {
    $qb = $this->createQueryBuilder('c')
               ->select('COUNT(c)')
               ->where('c.ip = :ip')
                 ->setParameter('ip', $ip)
               ->andWhere('c.date >= :date')
                 ->setParameter('date', new \Datetime('-'.$secondes.'seconds'));
    return (bool) $qb->getQuery()->getSingleScalarResult();
  }
}