<?php
class QuestionManager {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    public function processRequest($action, $data = null) {
        switch ($action) {
            case 'fetch':
                return $this->fetchQuestions();
            case 'save':
                return $this->saveQuestion($data);
            case 'delete':
                return $this->deleteQuestion($data);
            default:
                http_response_code(400);
                return "Invalid action";
        }
    }

    private function fetchQuestions() {
        $query = "SELECT * FROM questions";
        $result = $this->conn->query($query);

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return json_encode(array('data' => $data));
    }

    private function saveQuestion($data) {
        $id = $data['id'];
        $questionText = $data['questionText'];
        $optionA = $data['optionA'];
        $optionB = $data['optionB'];
        $optionC = $data['optionC'];
        $optionD = $data['optionD'];
        $correctOption = $data['correctOption'];

        if (empty($id)) {
            // เพิ่มข้อสอบใหม่
            $query = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option) 
                      VALUES ('$questionText', '$optionA', '$optionB', '$optionC', '$optionD', '$correctOption')";
        } else {
            // แก้ไขข้อสอบ
            $query = "UPDATE questions SET 
                      question_text='$questionText', option_a='$optionA', option_b='$optionB', 
                      option_c='$optionC', option_d='$optionD', correct_option='$correctOption' 
                      WHERE id=$id";
        }

        if ($this->conn->query($query) === TRUE) {
            return "success";
        } else {
            http_response_code(500);
            return "error";
        }
    }

    private function deleteQuestion($data) {
        $id = $data['id'];

        $query = "DELETE FROM questions WHERE id=$id";

        if ($this->conn->query($query) === TRUE) {
            return "success";
        } else {
            http_response_code(500);
            return "error";
        }
    }
}
?>
