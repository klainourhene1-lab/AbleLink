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
    $this->mot_de_passe = $mot_de_passe;
    $this->role = $role;
    $this->statut = $statut;
    $this->date_inscription = $date_inscription ?: date('Y-m-d H:i:s');
    $this->date_modification = $date_modification ?: date('Y-m-d H:i:s');
    $this->photo = $photo; // AJOUTE CETTE LIGNE
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

    // Setters


public function getPhoto() { return $this->photo; }
public function setPhoto($p) { $this->photo = $p; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setMotDePasse($password) { 
        $this->mot_de_passe = password_hash($password, PASSWORD_BCRYPT); 
    }
    public function setRole($role) { $this->role = $role; }
    public function setStatut($statut) { $this->statut = $statut; }

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