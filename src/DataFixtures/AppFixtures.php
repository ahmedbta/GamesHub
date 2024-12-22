<?php

namespace App\DataFixtures;

use App\Entity\Member;
use App\Entity\Coffre;
use App\Entity\Jeu;
use App\Entity\Bibliotheque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $members = $this->loadMembers($manager);

        $this->loadCoffres($manager, $members);
        $this->loadJeux($manager, $members);
        $this->loadBibliotheques($manager, $members);

        $manager->flush();
    }

    private function loadMembers(ObjectManager $manager): array
    {
        $memberData = [
            ['email' => 'user1@localhost', 'password' => 'userpass', 'nom' => 'Alice', 'roles' => ['ROLE_USER'], 'profilePicture' => 'user133.jpg'],
            ['email' => 'user2@localhost', 'password' => 'userpass', 'nom' => 'Bob', 'roles' => ['ROLE_USER'], 'profilePicture' => 'user233.jpg'],
            ['email' => 'admin@localhost', 'password' => 'adminpass', 'nom' => 'Admin', 'roles' => ['ROLE_ADMIN'], 'profilePicture' => 'admin33.jpg'],
        ];

        $members = [];
        foreach ($memberData as $index => $data) {
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $data['password']);
            $member->setEmail($data['email']);
            $member->setPassword($password);
            $member->setNom($data['nom']);
            $member->setRoles($data['roles']);
            $member->setProfilePicture($data['profilePicture']);
            $manager->persist($member);
            $this->addReference('member_' . $index, $member);
            $members[] = $member;
        }

        return $members;
    }

    private function loadCoffres(ObjectManager $manager, array $members): void
    {
        foreach ($members as $index => $member) {
            $coffre = new Coffre();
            $coffre->setNom('Coffre ' . ($index + 1) . ' de ' . $member->getNom());
            $coffre->setMembre($member);
            $manager->persist($coffre);
            $this->addReference('coffre_' . $index, $coffre);
        }
    }

    private function loadJeux(ObjectManager $manager, array $members): void
    {
        $jeuxData = [
            ['nom' => 'Monopoly', 'description' => 'Plongez dans le monde impitoyable de l\'immobilier avec Monopoly. Achetez, vendez et négociez pour bâtir votre empire, mais attention aux imprévus sur votre chemin vers la victoire.', 'nb_joueurs_max' => 8, 'nb_joueurs_mini' => 2, 'temps_jeu' => 120, 'type' => 'stratégie', 'annee' => 1935, 'imageName' => 'monopoly.jpg'],
            ['nom' => 'Uno', 'description' => 'Le jeu de cartes incontournable pour des soirées animées. Soyez le premier à vider votre main tout en contrant vos adversaires avec des cartes spéciales.', 'nb_joueurs_max' => 10, 'nb_joueurs_mini' => 2, 'temps_jeu' => 30, 'type' => 'cartes', 'annee' => 1971, 'imageName' => 'uno.jpg'],
            ['nom' => 'Chess', 'description' => 'Le jeu classique d\'échecs où la stratégie est reine. Affrontez vos adversaires et déployez vos meilleures tactiques pour un échec et mat.', 'nb_joueurs_max' => 2, 'nb_joueurs_mini' => 2, 'temps_jeu' => 60, 'type' => 'stratégie', 'annee' => 1475, 'imageName' => 'chess.jpg'],
            ['nom' => 'Ludo', 'description' => 'Un jeu familial amusant où la chance décide de votre progression. Faites avancer vos pions pour atteindre la victoire avant vos adversaires.', 'nb_joueurs_max' => 4, 'nb_joueurs_mini' => 2, 'temps_jeu' => 40, 'type' => 'hasard', 'annee' => 1896, 'imageName' => 'ludo.jpg'],
            ['nom' => 'Triominos', 'description' => 'Un jeu captivant mélangeant stratégie et réflexion. Placez vos tuiles triangulaires pour marquer un maximum de points.', 'nb_joueurs_max' => 6, 'nb_joueurs_mini' => 2, 'temps_jeu' => 45, 'type' => 'stratégie', 'annee' => 1965, 'imageName' => 'triominos.jpg'],
            ['nom' => 'Devine', 'description' => 'Testez votre créativité et votre intuition en devinant des mots ou des phrases. Un jeu idéal pour des moments de rire et de convivialité.', 'nb_joueurs_max' => 8, 'nb_joueurs_mini' => 2, 'temps_jeu' => 30, 'type' => 'party', 'annee' => 1990, 'imageName' => 'devine.jpg'],
            ['nom' => 'Lineup4', 'description' => 'Un duel stratégique où vous devez aligner quatre pièces de votre couleur avant votre adversaire. Simple à apprendre, difficile à maîtriser.', 'nb_joueurs_max' => 2, 'nb_joueurs_mini' => 2, 'temps_jeu' => 15, 'type' => 'stratégie', 'annee' => 1974, 'imageName' => 'lineup4.jpg'],
        ];
    
        foreach ($jeuxData as $index => $data) {
            $jeu = new Jeu();
            $jeu->setNom($data['nom']);
            $jeu->setDescription($data['description']);
            $jeu->setNbJoueursMax($data['nb_joueurs_max']);
            $jeu->setNbJoueursMini($data['nb_joueurs_mini']);
            $jeu->setTempsJeu($data['temps_jeu']);
            $jeu->setType($data['type']);
            $jeu->setAnnee($data['annee']); 
            $jeu->setImageName($data['imageName']);
            $jeu->setCoffre($this->getReference('coffre_' . ($index % count($members))));
            $manager->persist($jeu);
            $this->addReference('jeu_' . $index, $jeu);
        }
    }
    

    private function loadBibliotheques(ObjectManager $manager, array $members): void
    {
        foreach ($members as $index => $member) {
            for ($i = 1; $i <= 2; $i++) {
                $bibliotheque = new Bibliotheque();
                $bibliotheque->setDescription('Bibliothèque ' . $i . ' de ' . $member->getNom());
                $bibliotheque->setPubliee(true);
                $bibliotheque->setCreateur($member);
                foreach (array_rand(range(0, 6), 2) as $jeuIndex) {
                    $bibliotheque->addJeux($this->getReference('jeu_' . $jeuIndex));
                }
                $manager->persist($bibliotheque);
            }
        }
    }
}
