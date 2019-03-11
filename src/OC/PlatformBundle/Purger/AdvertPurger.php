<?php

namespace OC\PlatformBundle\Purger;

use Doctrine\ORM\EntityManagerInterface;

class AdvertPurger {

    /**
     * @var EntityManagerInterface
     */
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function purge($days) {

        $repositoty_advert = $this->em->getRepository('OCPlatformBundle:Advert');
        $repositoty_skill = $this->em->getRepository('OCPlatformBundle:AdvertSkill');

        $date = new \Datetime($days.' days ago');

        // Get les advert à supprimer
        $listAdvertsToDelete = $repositoty_advert->getAdvertsBefore($date);

        foreach ($listAdvertsToDelete as $advert) {

            // Candidatures liées à l'annonce
            $advertSkills = $repositoty_skill->findBy(array('advert' => $advert));

            // Suppression de ces candidatures
            foreach ($advertSkills as $advertSkill) {
                $this->em->remove($advertSkill);
            }

            // On remove l'advert
            $this->em->remove($advert);
        }

        $this->em->flush();
    }
}