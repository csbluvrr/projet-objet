name of the databate: hopitaldb <br />
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
you have to make these tables exactly so that the code works  <br />
also you have to create a new user in php my admin the username is jimmimoo and the password is 123456 (the user should have all rights)
