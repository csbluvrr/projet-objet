<?php
class Connection {
    private $host = "localhost";
    private $Adminname = "jimmimoo";
    private $password = "123456";
    private $dbname = "hopitaldb";
    public $conn;

    public function dbconnect() {
        $this->conn = new mysqli($this->host, $this->Adminname, $this->password);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }

    public function selectdb($dbName) {
        if ($this->conn->select_db($dbName)) {
            echo "";
        } else {
            echo "Error selecting database: " . $this->conn->error;
        }
    }

    public function createTable($query) {
        if ($this->conn->query($query) === TRUE) {
            echo "Table created successfully!";
        } else {
            echo "Error creating table: " . $this->conn->error;
        }
    }
}

class Admin {
    public $cin;
    public $firstname;
    public $lastname;
    public $num;
    public $email;
    public $password;
   

    public static $errorMsg = "";
    public static $successMsg = "";

    public function createAdmin( $cin, $firstname, $lastname, $num, $email, $password) {

        $this->cin = $cin;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->num = $num;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        
    }

    public function insertAdmin($tableName, $conn) {
        $sql = "INSERT INTO $tableName (cin, firstname, lastname, num, email, password) VALUES ( ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $this->cin, $this->firstname, $this->lastname, $this->num, $this->email, $this->password);
            
            if ($stmt->execute()) {
                self::$successMsg = "Admin created successfully!";
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
    }

    public static function selectAllAdmins($tableName, $conn) {
        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            self::$errorMsg = "No Admins found.";
        }

        return $data;
    }

    public static function selectAdminBycin($tableName, $conn, $cin) {
        $sql = "SELECT * FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                self::$errorMsg = "Admin not found.";
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return null;
    }

    public static function updateAdmin($Admin, $tableName, $conn, $cin) {
        // If a new password is provided, hash it
        if (!empty($Admin->password)) {
            $hashedPassword = password_hash($Admin->password, PASSWORD_DEFAULT);
        } else {
            // Otherwise, retain the existing password hash
            $sql = "SELECT password FROM $tableName WHERE cin = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("s", $cin);
                $stmt->execute();
                $stmt->bind_result($existingPasswordHash);
                $stmt->fetch();
                $stmt->close();
                
                // Use the existing password if no new password is provcined
                $hashedPassword = $existingPasswordHash;
            } else {
                self::$errorMsg = "Error preparing statement: " . $conn->error;
                return false;
            }
        }

        // Prepare and execute the update statement
        $sql = "UPDATE $tableName SET   firstname = ?, lastname = ?, num = ?, email = ?, password = ? WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssss", $Admin->firstname, $Admin->lastname,  $Admin->num, $Admin->email, $hashedPassword, $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Admin updated successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }

    public static function deleteAdmin($tableName, $conn, $cin) {
        $sql = "DELETE FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Admin deleted successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }
}

class Doctor {
    public $cin;
    public $firstname;
    public $lastname;
    public $specialite;
    public $num;
    public $email;
    public $password;
   

    public static $errorMsg = "";
    public static $successMsg = "";

    public function createDoctor( $cin, $firstname, $lastname, $specialite, $num, $email, $password) {

        $this->cin = $cin;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->specialite = $specialite;
        $this->num = $num;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        
    }

    public function insertDoctor($tableName, $conn) {
        $sql = "INSERT INTO $tableName (cin, firstname, lastname, specialite, num, email, password) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssss", $this->cin, $this->firstname, $this->lastname, $this->specialite, $this->num, $this->email, $this->password);
            
            if ($stmt->execute()) {
                self::$successMsg = "Doctor created successfully!";
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
    }

    public static function selectAllDoctors($tableName, $conn) {
        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            self::$errorMsg = "No Doctors found.";
        }

        return $data;
    }

    public static function selectDoctorBycin($tableName, $conn, $cin) {
        $sql = "SELECT * FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                self::$errorMsg = "Doctor not found.";
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return null;
    }

    public static function updateDoctor($Doctor, $tableName, $conn, $cin) {
        // If a new password is provided, hash it
        if (!empty($Doctor->password)) {
            $hashedPassword = password_hash($Doctor->password, PASSWORD_DEFAULT);
        } else {
            // Otherwise, retain the existing password hash
            $sql = "SELECT password FROM $tableName WHERE cin = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("s", $cin);
                $stmt->execute();
                $stmt->bind_result($existingPasswordHash);
                $stmt->fetch();
                $stmt->close();
                
                // Use the existing password if no new password is provcined
                $hashedPassword = $existingPasswordHash;
            } else {
                self::$errorMsg = "Error preparing statement: " . $conn->error;
                return false;
            }
        }

        // Prepare and execute the update statement
        $sql = "UPDATE $tableName SET   firstname = ?, lastname = ?, specialite = ?, num = ?, email = ?, password = ? WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssssss", $Doctor->firstname, $Doctor->lastname, $Doctor->specialite,  $Doctor->num, $Doctor->email, $hashedPassword, $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Doctor updated successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }

    public static function deleteDoctor($tableName, $conn, $cin) {
        $sql = "DELETE FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Doctor deleted successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }
}

class Patient {
    public $cin;
    public $firstname;
    public $lastname;
    public $age;
    public $bloodtype;
    public $pcondition;
    public $observation;
    public $num;
    public $email;
   

    public static $errorMsg = "";
    public static $successMsg = "";

    public function createPatient( $cin, $firstname, $lastname, $age, $bloodtype, $pcondition, $observation, $num, $email) {

        $this->cin = $cin;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->age = $age;
        $this->bloodtype = $bloodtype;
        $this->pcondition = $pcondition;
        $this->observation = $observation;
        $this->num = $num;
        $this->email = $email;
        
    }

    public function insertPatient($tableName, $conn) {
        $sql = "INSERT INTO $tableName (cin, firstname, lastname, age, bloodtype, pcondition, observation, num, email) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssisssss", $this->cin, $this->firstname, $this->lastname, $this->age, $this->bloodtype, $this->pcondition, $this->observation, $this->num, $this->email);
            
            if ($stmt->execute()) {
                self::$successMsg = "Patient created successfully!";
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
    }

    public static function selectAllPatients($tableName, $conn) {
        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            self::$errorMsg = "No Patients found.";
        }

        return $data;
    }

    public static function selectPatientBycin($tableName, $conn, $cin) {
        $sql = "SELECT * FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                self::$errorMsg = "Patient not found.";
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return null;
    }

    public static function updatePatient($Patient, $tableName, $conn, $cin) {

        // Prepare and execute the update statement
        $sql = "UPDATE $tableName SET   firstname = ?, lastname = ?, age = ?, bloodtype = ?, pcondition = ?, observation = ?, num = ?, email = ? WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssissssss", $Patient->firstname, $Patient->lastname, $Patient->age, $Patient->bloodtype, $Patient->pcondition, $Patient->observation,  $Patient->num, $Patient->email, $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Patient updated successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }

    public static function deletePatient($tableName, $conn, $cin) {
        $sql = "DELETE FROM $tableName WHERE cin = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $cin);

            if ($stmt->execute()) {
                self::$successMsg = "Patient deleted successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
        return false;
    }
}

class Rendezvous {
    public $id;
    public $date_rdv;
    public $heure;
    public $id_patient;
    public $id_doctor;

    public static $errorMsg = "";
    public static $successMsg = "";

    public function createRendezvous($date_rdv, $heure, $id_patient, $id_doctor) {
        $this->date_rdv = $date_rdv;
        $this->heure = $heure;
        $this->id_patient = $id_patient;
        $this->id_doctor = $id_doctor;
    }

    public function insertRendezvous($tableName, $conn) {
        $sql = "INSERT INTO $tableName (date_rdv, heure, id_patient, id_doctor) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $this->date_rdv, $this->heure, $this->id_patient, $this->id_doctor);

            if ($stmt->execute()) {
                self::$successMsg = "Rendezvous created successfully!";
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }
    }

    public static function selectAllRendezvous($tableName, $conn) {
        $sql = "SELECT * FROM $tableName";
        $result = $conn->query($sql);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            self::$errorMsg = "No rendezvous found.";
        }

        return $data;
    }

    public static function selectRendezvousById($tableName, $conn, $id) {
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                self::$errorMsg = "Rendezvous not found.";
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return null;
    }

    public static function updateRendezvous($Rendezvous, $tableName, $conn, $id) {
        $sql = "UPDATE $tableName SET date_rdv = ?, heure = ?, id_patient = ?, id_doctor = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssi", $Rendezvous->date_rdv, $Rendezvous->heure, $Rendezvous->id_patient, $Rendezvous->id_doctor, $id);

            if ($stmt->execute()) {
                self::$successMsg = "Rendezvous updated successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return false;
    }

    public static function deleteRendezvous($tableName, $conn, $id) {
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                self::$successMsg = "Rendezvous deleted successfully!";
                return true;
            } else {
                self::$errorMsg = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            self::$errorMsg = "Error preparing statement: " . $conn->error;
        }

        return false;
}
}

?>

