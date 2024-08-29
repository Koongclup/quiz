<?php
class User {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getUserCounts() {
        $counts = [];

        // Get total users
        $result = $this->conn->query("SELECT COUNT(*) as total_users FROM users");
        $row = $result->fetch_assoc();
        $counts['total_users'] = $row['total_users'];

        // Get quiz takers
        $result = $this->conn->query("SELECT COUNT(*) as quiz_takers FROM results");
        $row = $result->fetch_assoc();
        $counts['quiz_takers'] = $row['quiz_takers'];

        $result = $this->conn->query("SELECT COUNT(*) as quiz FROM questions");
        $row = $result->fetch_assoc();
        $counts['quiz'] = $row['quiz'];

        return $counts;
    }

    public function getUsers() {
        $result = $this->conn->query("SELECT id, username, role_id FROM users");
        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    public function createUser($username, $password, $role_id) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $passwordHash, $role_id);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to create user'];
        }

        $stmt->close();
    }

    public function updateUser($id, $username, $password, $role_id) {
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE users SET username = ?, password = ?, role_id = ? WHERE id = ?");
            $stmt->bind_param("ssii", $username, $passwordHash, $role_id, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE users SET username = ?, role_id = ? WHERE id = ?");
            $stmt->bind_param("sii", $username, $role_id, $id);
        }

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update user'];
        }

        $stmt->close();
    }

    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to delete user'];
        }

        $stmt->close();
    }
}
?>
