<?php
namespace App\Models;

class User {
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;
    private string $role;
    private string $createdAt;
    private string $updatedAt;
    
    public function __construct(
        ?int $id = null,
        string $name = '',
        string $email = '',
        string $password = '',
        string $role = 'user',
        string $createdAt = '',
        string $updatedAt = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getPassword(): string {
        return $this->password;
    }
    
    public function getRole(): string {
        return $this->role;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }
    
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setName(string $name): void {
        $this->name = $name;
    }
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    
    public function setPassword(string $password): void {
        $this->password = $password;
    }
    
    public function setRole(string $role): void {
        $this->role = $role;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
    
    public function setUpdatedAt(string $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
}
