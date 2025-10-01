<?php
class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $telephone;
    public $date_creation;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lire tous les utilisateurs
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nom, prenom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Créer un utilisateur
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":prenom", $this->prenom);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire un utilisateur spécifique
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->telephone = $row['telephone'];
            $this->date_creation = $row['date_creation'];
            return true;
        }
        return false;
    }

    // Mettre à jour un utilisateur
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Supprimer un utilisateur
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Rechercher des utilisateurs
    public function search($keywords)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ?
                 ORDER BY nom, prenom";

        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);

        $stmt->execute();
        return $stmt;
    }

    // Vérifier si l'email existe déjà
    public function emailExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        if ($this->id) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        if ($this->id) {
            $stmt->bindParam(':id', $this->id);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
