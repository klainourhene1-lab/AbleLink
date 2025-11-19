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

    public function __construct($id = null, $prenom = "", $nom = "", $email = "", $telephone = "", $role = "candidat", $statut = "actif", $date_inscription = null, $date_modification = null) {
        $this->id = $id;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->role = $role;
        $this->statut = $statut;
        $this->date_inscription = $date_inscription ?: date('Y-m-d H:i:s');
        $this->date_modification = $date_modification ?: date('Y-m-d H:i:s');
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

    // Setters - أضف الدوال الناقصة
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($mot_de_passe) { 
        $this->mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setRole($role) { $this->role = $role; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateInscription($date) { $this->date_inscription = $date; }
    public function setDateModification($date) { $this->date_modification = $date; }

    // Verify password
    public function verifyPassword($password) {
        return password_verify($password, $this->mot_de_passe);
    }
    
}
?>