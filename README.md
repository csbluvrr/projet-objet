name dyal databate: hopitaldb <br />
CREATE TABLE admins ( <br />
    cin CHAR(8) PRIMARY KEY, <br />
    firstname VARCHAR(50) NOT NULL, <br />
    lastname VARCHAR(50) NOT NULL, <br />
    num CHAR(10), <br />
    email VARCHAR(100) NOT NULL, <br />
    password VARCHAR(255) NOT NULL <br />
);

CREATE TABLE doctors ( <br />
    cin CHAR(8) PRIMARY KEY, <br />
    firstname VARCHAR(50) NOT NULL, <br />
    lastname VARCHAR(50) NOT NULL, <br />
    specialite VARCHAR(30) NOT NULL, <br />
    num CHAR(10), <br />
    email VARCHAR(100) NOT NULL, <br />
    password VARCHAR(255) NOT NULL <br />
); <br />

CREATE TABLE patients ( <br />
    cin CHAR(8) PRIMARY KEY, <br />
    firstname VARCHAR(50) NOT NULL, <br />
    lastname VARCHAR(50) NOT NULL, <br />
    age INT NOT NULL,<br />
    bloodtype VARCHAR(4) NOT NULL, <br />
    pcondition VARCHAR(200) NOT NULL, <br />
    observation VARCHAR(300) NOT NULL, <br />
    num CHAR(10), <br />
    email VARCHAR(100) <br />
);

CREATE TABLE rendezvous (
    id INT(11) PRIMARY KEY AUTO_INCREMENT, <br />
    date_rdv DATE NOT NULL, <br />
    heure TIME NOT NULL, <br />
    id_patient CHAR(8) NOT NULL,   <br />
    id_doctor CHAR(8) NOT NULL  <br />
); <br />
derou hadou exactly f phpmyadmin with the same names bach ikhdem likoum lcode
