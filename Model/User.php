<?php
class User {
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $mot_de_passe;
    private $telephone;
    private $role;
    private $statut;
    private $date_inscription;
    private $date_modification;
    private $photo;

    public function __construct($id=null, $nom="", $prenom="", $email="", $telephone="", $mot_de_passe="", $role="Utilisateur", $statut="actif", $date_inscription=null, $date_modification=null, $photo=null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        // Hash password only for new users (when id is null) AND the password is not already hashed
        // Bcrypt hashes start with $ and are at least 60 chars long
        $isAlreadyHashed = $mot_de_passe && strlen($mot_de_passe) >= 60 && substr($mot_de_passe, 0, 1) === '$';
        if ($mot_de_passe && !$id && !$isAlreadyHashed) {
            $this->mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        } else {
            $this->mot_de_passe = $mot_de_passe;
        }
        $this->role = $role;
        $this->statut = $statut;
        $this->date_inscription = $date_inscription ?: date('Y-m-d H:i:s');
        $this->date_modification = $date_modification ?: date('Y-m-d H:i:s');
        $this->photo = $photo;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() { return $this->mot_de_passe; }
    public function getTelephone() { return $this->telephone; }
    public function getRole() { return $this->role; }
    public function getStatut() { return $this->statut; }
    public function getDateInscription() { return $this->date_inscription; }
    public function getDateModification() { return $this->date_modification; }
    public function getPhoto() { return $this->photo; }

    // Setters
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setMotDePasse($password) { 
        $this->mot_de_passe = password_hash($password, PASSWORD_BCRYPT); 
    }
    public function setRole($role) { $this->role = $role; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setPhoto($p) { $this->photo = $p; }

    // Vérifie le mot de passe
    public function verifyPassword($password) {
        return password_verify($password, $this->mot_de_passe);
    }

    // For loading existing hashed password
    public function loadHashedPassword($hashedPassword) {
        $this->mot_de_passe = $hashedPassword;
    }
}
?>