<?php
    session_start();
    if(!isset($_SESSION["id"])){
        header("Location: login");
        die();
    }
    include "config.php";
    $sql = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
    $sql->execute();
    if($sql->rowCount()==1){
        $name = $sql->fetch(PDO::FETCH_NUM)[0];
    }else{
        session_destroy();
        header("Location: login");
    }

    if(isset($_GET["id"])){
        $id = $_GET["id"];
        $own = false;
    }else{
        $id = $_SESSION["id"];
        $own = true;
    }
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["profile-submit"])){
            $picture_name = $_SESSION["id"].time();
            if(isset($_FILES["profile-picture"]) && $_FILES["profile-picture"]["error"] == 0){
                $image = time();
                $path = "uploads/profile-pictures/".$image.".webp";
                move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $path);
            }else{
                $sql = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
                $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
                $sql->execute();
                $image = $sql->fetch(PDO::FETCH_NUM)[0];
            }
            
            try{
                $sql = $conn->prepare("UPDATE users SET name = ? , profile_picture = ?, about = ? WHERE id = ?");
                $sql->bindParam(1, $_POST["name"], PDO::PARAM_STR);
                $sql->bindParam(2, $image, PDO::PARAM_STR);
                $sql->bindParam(3, $_POST["about"], PDO::PARAM_STR);
                $sql->bindParam(4, $_SESSION["id"], PDO::PARAM_INT);
                try{
                    $sql->execute();
                    $feedback = array(true, "Profile updated successfully");
                }catch(PDOException $e){
                    echo $e;
                    $feedback = array(false, "Couldn't update profile");
                }
            }catch(PDOException $e){
                $feedback = array(false, "Couldn't upload image");
            }
        }else if(isset($_POST["skill-submit"])){
            $sql = $conn->prepare("INSERT INTO user_skills(user, exp, skill) VALUES(?, ?, ?)");
            $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
            $sql->bindParam(2, $_POST["exp"], PDO::PARAM_INT);
            $sql->bindParam(3, $_POST["skill"], PDO::PARAM_STR);
            try{
                $sql->execute();
                $feedback = array(false, "Skill added successfully");
            
            }catch(PDOException $e){
                $feedback = array(false, "Coudn't add skill");
            }
        }else if(isset($_POST["requirement-submit"])){
            $sql = $conn->prepare("INSERT INTO requirements(user, skill, business) VALUES(?, ?, ?)");
            $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
            $sql->bindParam(2, $_POST["skill"], PDO::PARAM_INT);
            $sql->bindParam(3, $_POST["business"], PDO::PARAM_STR);
            try{
                $sql->execute();
                $feedback = array(false, "Requirement created successfully");
            
            }catch(PDOException $e){
                $feedback = array(false, "Coudn't create requirement");
            }
        }
    }
    $sql = $conn->prepare("SELECT name, profile_picture, about, type, email FROM users WHERE id = ?");
    $sql->bindParam(1, $id, PDO::PARAM_INT);
    $sql->execute();
    if($sql->rowCount()==1){
        $user = $sql->fetch(PDO::FETCH_NUM);
        if($user[1]==NULL){
            $user[1]="profile-picture-alternative";
        }
    }else{
        session_destroy();
        header("Location: login");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | VyaparGuru</title>
    <link rel="shortcut icon" href="assets/public/branding/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/public/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="sticky-top border-bottom shadow-sm bg-white">
        <nav class="navbar navbar-expand">
            <div class="container-xl">
                <a href="#" class="navbar-brand d-none d-sm-block"><svg xmlns="http://www.w3.org/2000/svg" width=162 viewBox="0 0 96 18"><path d="M.96 12.94c-.2-.13-.34-.3-.39-.52-.06-.22-.1-.58-.12-1.08L.26 7.75.13 5.23 0 1.15C0 .84.14.57.41.36S1.04.04 1.46.04c.38 0 .67.11.85.32.18.22.27.54.27.99l.02 7.77v1.93L6.12 3.8C7.43 1.27 8.28 0 8.65 0c.47 0 .86.11 1.15.33s.45.48.46.78l-.94 1.8-.9 1.62-.66 1.23-2.71 4.98c-.51.91-.94 1.56-1.29 1.93-.15.18-.39.34-.7.46s-.66.18-1.03.18c-.51-.11-.86-.24-1.07-.37zm11.97.07c-.41.2-.87.3-1.38.3-.85 0-1.5-.27-1.95-.8-.46-.53-.68-1.24-.68-2.13a3.48 3.48 0 0 1 .12-.86l1.29-5.09c.08-.29.22-.5.41-.63.2-.14.52-.2.97-.2.67 0 1 .19 1 .57 0 .16-.05.4-.13.74l-1.29 5.23c-.04.16-.06.31-.06.47 0 .27.07.5.21.66.14.17.37.25.67.25.4 0 .83-.12 1.29-.35s.85-.52 1.17-.86l.37-1.54.84-3.38c.14-.52.32-.95.55-1.28s.58-.5 1.07-.5c.68 0 1.02.21 1.02.62 0 .07-.02.15-.05.24l-.07.24-.64 2.69-1.31 5.45c-.33 1.3-.71 2.32-1.13 3.06s-.95 1.28-1.59 1.6c-.63.33-1.44.49-2.42.49-.63 0-1.14-.09-1.55-.27s-.7-.4-.89-.66-.28-.5-.28-.72c0-.64.35-.96 1.04-.96a.84.84 0 0 1 .47.12 1.83 1.83 0 0 1 .37.33 2.82 2.82 0 0 0 .53.41c.16.09.38.14.66.14.22 0 .46-.09.73-.27s.53-.47.79-.86.47-.88.63-1.46l.48-1.7c-.42.4-.85.71-1.26.91zm13.34-.2c-.03.08-.14.16-.34.23-.2.08-.37.12-.51.12-.46 0-.79-.07-.99-.2-.2-.14-.3-.34-.3-.61a4.13 4.13 0 0 1 .08-.78c-.28.39-.7.71-1.24.97-.54.25-.98.38-1.32.38-.86 0-1.57-.25-2.14-.76s-.86-1.32-.86-2.44c0-1.06.25-2.05.74-2.98s1.17-1.68 2.05-2.25c.87-.57 1.84-.85 2.9-.85.2 0 .47.06.81.17a3.35 3.35 0 0 1 1.01.57c.34.27.62.61.86 1.02.1 0 .22.05.36.16.14.1.21.21.21.31 0 .12-.02.23-.06.35l-1.4 5.68a7.58 7.58 0 0 0 .14.91zM24.82 5.7c-.23-.19-.43-.29-.61-.29-.59 0-1.14.21-1.66.63s-.94.95-1.26 1.57-.48 1.22-.48 1.78c0 .55.06 1 .19 1.35a.74.74 0 0 0 .75.53c.32 0 .69-.1 1.1-.29a3.99 3.99 0 0 0 1.12-.79c.33-.33.54-.68.64-1.04l.65-2.71a1.29 1.29 0 0 0-.44-.74zm5.25 8.48l-.68 2.55c-.05.14-.2.25-.46.32s-.44.11-.56.11c-.58 0-.88-.36-.92-1.07l1.23-6 1.5-6.18c.03-.19.14-.39.36-.58.21-.19.4-.28.57-.28.29 0 .54.09.74.26s.32.43.36.75c.51-.35 1.1-.53 1.77-.53.72 0 1.37.16 1.96.48a3.48 3.48 0 0 1 1.39 1.38c.34.6.51 1.3.51 2.09 0 .98-.19 1.9-.56 2.77a5.09 5.09 0 0 1-1.67 2.13c-.74.55-1.64.82-2.69.82-.33 0-.76-.11-1.29-.34s-.87-.5-1.04-.81l-.52 2.13zm1.06-4.28a1.4 1.4 0 0 0 .32.86c.2.25.43.44.7.58s.49.2.67.2c.56 0 1.06-.2 1.49-.6s.76-.91.99-1.52.35-1.21.35-1.8c0-.77-.19-1.35-.58-1.75-.38-.4-.86-.59-1.42-.59-.29 0-.59.06-.87.18a2.09 2.09 0 0 0-.76.53l-.85 3.59-.04.32zm14.49 3.15c-.33.09-.61.13-.86.13-.23 0-.38-.05-.45-.17-.07-.11-.13-.28-.18-.5l-.1-.45c-.55.38-1.11.67-1.68.87s-1.05.3-1.43.3c-.33 0-.71-.11-1.12-.33a3.3 3.3 0 0 1-1.07-.9c-.3-.38-.44-.79-.44-1.25 0-.73.24-1.66.71-2.79S40.14 5.84 41 5c.86-.85 1.87-1.27 3.03-1.27 1.01 0 1.91.16 2.71.49.8.32 1.19.78 1.19 1.38-.36.68-.75 1.69-1.16 3.04-.42 1.36-.62 2.36-.62 3.01 0 .43.03.74.1.92-.1.24-.31.4-.63.48zM44.6 8.39a14.17 14.17 0 0 1 1.01-2.37c-.07-.27-.27-.44-.61-.52a4.06 4.06 0 0 0-.91-.13c-.47 0-.99.32-1.54.98-.55.65-1.02 1.4-1.41 2.27s-.59 1.56-.59 2.1c0 .26.07.45.21.56a.85.85 0 0 0 .54.17c.29 0 .76-.11 1.41-.34s1.1-.45 1.35-.67c.11-.59.29-1.27.54-2.05zm6.02 4.24c-.03.22-.13.38-.29.47s-.43.14-.8.14c-.42 0-.74-.05-.94-.14s-.31-.29-.33-.59l.5-2.77.36-2 .38-1.94c.13-.92.31-1.53.54-1.83.23-.29.51-.44.85-.44.45 0 .75.07.89.19a.69.69 0 0 1 .24.51l-.15 1.15a4.98 4.98 0 0 1 1.37-1.26c.53-.33 1-.5 1.42-.5.56 0 1.01.1 1.33.3s.48.46.48.77c0 .23-.14.42-.41.55-.28.13-.64.22-1.09.25-.71.07-1.4.37-2.1.92-.69.55-1.19 1.19-1.5 1.93l-.69 3.81-.06.48z" fill="#00796b"/><path d="M66.18 6.06c.25.19.41.42.47.68.06.27.1.61.1 1.03 0 1.09-.3 2.06-.88 2.9-.59.84-1.36 1.49-2.31 1.95a6.82 6.82 0 0 1-3.02.69c-1.54 0-2.67-.44-3.38-1.31-.72-.87-1.08-1.94-1.08-3.2 0-1.54.29-2.98.88-4.33s1.35-2.44 2.3-3.25C60.2.41 61.17 0 62.17 0c.94 0 1.74.14 2.4.43.67.29 1.17.66 1.5 1.12s.5.94.5 1.43c0 .4-.13.72-.38.94-.26.22-.63.33-1.11.33-.38 0-.66-.15-.82-.44s-.24-.61-.24-.97c0-.17-.1-.34-.3-.53s-.44-.27-.74-.27c-.7 0-1.41.32-2.11.97-.71.64-1.28 1.52-1.73 2.64-.45 1.11-.67 2.33-.67 3.66 0 .61.21 1.1.63 1.47s.98.56 1.67.56c.5 0 1.04-.16 1.61-.47.58-.31 1.06-.73 1.46-1.25s.6-1.08.6-1.68c-.13.02-.29.06-.48.12-.24.08-.46.14-.66.18s-.47.06-.82.06c-.41 0-.71-.05-.91-.17-.2-.11-.3-.31-.3-.59 0-.2.05-.42.14-.68.1-.26.21-.45.36-.57l1.63-.38c.46-.08 1-.13 1.61-.13.53 0 .92.09 1.17.28zm9.25 6.85c-.17.09-.41.13-.7.13-.9 0-1.35-.2-1.35-.6l.81-2.27c-.53 1.08-1.14 1.87-1.84 2.38-.71.51-1.37.76-1.98.76-.79 0-1.35-.25-1.68-.75s-.49-1.23-.49-2.2c0-.2.03-.44.1-.72l1.21-5.21c.06-.29.19-.5.38-.63.19-.14.51-.2.96-.2.68 0 1.02.19 1.02.57 0 .1-.04.35-.11.74l-1.19 5.23a1.99 1.99 0 0 0-.12.64c0 .49.19.74.58.74.36 0 .84-.41 1.46-1.23.61-.82 1.2-1.78 1.76-2.89s.91-2.02 1.07-2.73c.06-.29.13-.5.2-.63.07-.14.2-.24.38-.31s.45-.11.81-.11c.69 0 1.04.21 1.04.62 0 .09-.04.25-.11.49l-.96 3.92-.88 3.81c-.09.22-.2.37-.37.45zm5.11-.29c-.05.23-.15.39-.33.48-.17.09-.45.14-.82.14-.42 0-.73-.05-.92-.14s-.29-.29-.29-.59l.92-3.73 1.02-4.1-.48-.58c-.04-.04-.06-.08-.06-.14 0-.13.14-.24.42-.32s.67-.13 1.17-.13c.45 0 .75.13.9.38s.25.58.29.99l-.13.51a5.52 5.52 0 0 1 1.43-1.26c.54-.33 1.03-.5 1.45-.5.55 0 .98.1 1.29.29.31.2.46.45.46.76 0 .27-.09.56-.28.85s-.42.54-.7.74-.56.32-.83.34c-.26 0-.43-.03-.52-.1s-.13-.16-.13-.29l.12-.64c-1.03.29-1.95.98-2.79 2.07l-1.12 4.51c0 .08-.03.23-.07.46zm13.06.68c-.18.09-.42.13-.71.13-.88 0-1.33-.2-1.33-.6l.12-.74c-.36.27-.78.55-1.27.82s-.89.41-1.21.41c-1.9 0-2.85-.63-2.85-1.89 0-2.15.64-4.48 1.9-6.99.15-.3.32-.51.49-.64s.47-.2.89-.2c.45 0 .79.06 1.01.19.22.12.34.27.34.44 0 .04-.07.16-.21.35a3.98 3.98 0 0 0-.36.51c-1.2 2.04-1.81 4.03-1.81 5.97 0 .17.06.32.19.45s.24.19.35.19c.28 0 .76-.14 1.43-.41s1.19-.55 1.55-.84l.38-1.58.86-3.48c.14-.52.32-.95.55-1.28s.58-.5 1.07-.5c.68 0 1.02.21 1.02.62 0 .07-.02.15-.05.24l-.07.24-.77 3.16-1.17 4.96c-.05.24-.16.39-.34.47z" fill="#f57c00"/></svg></a>
                <a href="#" class="navbar-brand d-sm-none"><svg xmlns="http://www.w3.org/2000/svg" width=48 viewBox="0 0 96 64"><path d="M4.65 62.21c-.99-.63-1.63-1.45-1.91-2.49s-.48-2.77-.6-5.21c-.12-3.69-.44-9.44-.93-17.27L.56 25.13C.19 18.61.01 12.07 0 5.53c0-1.51.67-2.77 2-3.8S5.03.18 7.07.18c1.87 0 3.23.52 4.09 1.55.87 1.03 1.31 2.61 1.31 4.73l.09 37.35v9.29c4.96-11.07 10.64-22.69 17.03-34.87C35.97 6.08 40.07 0 41.87 0c2.29 0 4.16.53 5.59 1.6s2.17 2.32 2.23 3.76c-.37.81-1.89 3.69-4.56 8.64l-4.37 7.79-3.16 5.91-13.13 23.93c-2.48 4.37-4.56 7.48-6.23 9.29-.75.88-1.88 1.61-3.4 2.2s-3.19.88-4.97.88c-2.5-.56-4.23-1.16-5.22-1.79z" fill="#00796b"/><path d="M93.25 29.13c1.21.91 1.97 2 2.28 3.28s.47 2.92.47 4.93c0 5.25-1.43 9.89-4.28 13.93s-6.57 7.16-11.16 9.39C75.97 62.88 71.11 64 65.96 64c-7.44 0-12.89-2.09-16.37-6.29-3.48-4.19-5.21-9.32-5.21-15.39 0-7.39 1.43-14.32 4.28-20.83S55.21 9.77 59.77 5.87 69.03 0 73.87 0c4.52 0 8.4.69 11.63 2.07 3.23 1.37 5.64 3.17 7.25 5.4s2.41 4.52 2.41 6.89c0 1.95-.63 3.44-1.87 4.51s-3.04 1.6-5.4 1.6c-1.87 0-3.17-.71-3.95-2.11-.77-1.41-1.16-2.96-1.16-4.64 0-.81-.48-1.65-1.44-2.53s-2.16-1.32-3.59-1.32c-3.41 0-6.83 1.55-10.23 4.64-3.41 3.09-6.2 7.32-8.37 12.67s-3.25 11.21-3.25 17.6c0 2.95 1.03 5.31 3.07 7.08s4.75 2.68 8.09 2.68c2.41 0 5.03-.75 7.81-2.25 2.79-1.51 5.15-3.51 7.07-6 1.92-2.51 2.88-5.19 2.88-8.07-.63.07-1.4.25-2.32.56-1.17.37-2.25.65-3.21.84s-2.28.28-3.95.28c-1.99 0-3.45-.27-4.41-.8s-1.44-1.48-1.44-2.87c0-.93.23-2.03.69-3.28.47-1.25 1.04-2.16 1.72-2.72 3.04-.81 5.68-1.43 7.91-1.83s4.84-.61 7.81-.61c2.55-.02 4.43.44 5.63 1.34z" fill="#f57c00"/></svg></a>
                <div class="collapse navbar-collapse flex-grow-0">
                    <ul class="navbar-nav d-none d-md-flex">
                        <li class="nav-item"><a href="home" class="nav-link"><i class="fa-solid fa-house me-2"></i><b>Home</b></a></li>
                        <li class="nav-item"><a href="search" class="nav-link"><i class="fa-solid fa-magnifying-glass me-2"></i><b>Search</b></a></li>
                        <li class="nav-item"><a href="messages" class="nav-link"><i class="fa-regular fa-envelope me-2"></i><b>Messages</b></a></li>
                        <li class="nav-item"><a href="notifications" class="nav-link"><i class="fa-regular fa-bell me-2"></i><b>Notifications</b></a></li>
                        <li class="nav-item dropdown">
                            <a href="profile" class="nav-link active dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-regular fa-circle-user fa-lg me-2"></i><b><?php echo $name ?></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="profile" class="dropdown-item">Profile</a></li>
                                <li><a href="settings" class="dropdown-item">Settings</a></li>
                                <li><a href="settings" class="dropdown-item">Help</a></li>
                                <li><a href="logout" class="dropdown-item">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <a href="profile" class="btn border-0 d-md-none"  data-bs-toggle="offcanvas" data-bs-target="#aside-right"><i class="fa-regular fa-circle-user fa-xl"></i></a>
        </nav>
    </header>
    <main class="flex-grow-1 bg-body-secondary">
        <article class="container-xxl py-3">
            <form action=<?php echo $_SERVER["PHP_SELF"];?> method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 400px">
                <div class="w-50 mx-auto"><label for="profile-picture" class="d-block ratio ratio-1x1"><img src="uploads/profile-pictures/<?php echo $user[1]; ?>.webp" alt="<?php echo $user[0]; ?>" class="img-thumbnail rounded-circle object-fit-cover"></label></div>
                <input type="file" id="profile-picture" name="profile-picture" class="d-none">
                <div class="text-center mt-2"><span class="border border-success text-success p-1">
                <?php
                    if($user["3"]==0){
                        echo "VOLUNTEER";
                    }else{
                        echo "BUSINESS";
                    }
                ?>
                </span></div>
                <div class="text-center mt-3"><?php if($own == false){echo "<a href='mailto:".$user[4]."' class='text-decoration-none'><i class='fa-solid fa-paper-plane me-2'></i><span>Send a message</span></a>";}?></div>
                <div class="form-floating my-3">
                    <input type="text" id="name" name="name" class="form-control" placeholder="Full name" autocomplete="off" spellcheck="false" value="<?php echo $user[0]; ?>" required <?php if($own == false){echo "readonly"; }?>>
                    <label for="name">Full name</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea id="about" name="about" class="form-control" placeholder="About" autocomplete="off" style="height:100px; resize: none" <?php if($own == false){echo "readonly"; }?>><?php echo $user[2]; ?></textarea>
                    <label for="about">About</label>
                </div>
                <?php
                    if($own == true){
                ?>
                <div><input type="submit" name="profile-submit" class="btn btn-success w-100" value="Save changes"></div>
                <?php }
                ?>
            </form>
            <hr>
            <section>
                <div class="mx-auto" style="max-width: 600px">
                    <?php
                        if($user["3"]==0){
                    ?>
                    <h2>Skillset</h2>
                    <?php
                        if($own == true){
                    ?>
                    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="input-group mb-3">
                        <select name="skill" class="form-select" required>
                            <option value="" selected disabled>Skill</option>
                            <?php
                                $sql = $conn->prepare("SELECT * FROM skillset");
                                $sql->execute();
                                while($row = $sql->fetch(PDO::FETCH_NUM)){
                                    echo "<option value=".$row[0].">".$row[1]."</option>";
                                }
                            ?>
                        </select>
                        <select name="exp" class="form-select" required>
                            <option value="" selected disabled>Experience (yrs)</option>
                            <?php
                                for($i = 0; $i <= 10; $i++){
                                    echo "<option value=".$i.">".$i."</option>";
                                }
                            ?>
                        </select>
                        <input type="submit" name="skill-submit" class="btn btn-success" value="Add">
                    </form>
                    <?php }?>
                    <table class="table table-bordered table-striped table-hover mx-auto">
                        <tr>
                            <th class="text-center">Sl</th>
                            <th class="text-center">Skill</th>
                            <th class="text-center">Exp</th>
                        </tr>
                        <?php
                            $sql = $conn->prepare("SELECT s.name, us.exp FROM user_skills us JOIN skillset s ON us.skill = s.id WHERE us.user = ?");
                            $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
                            $sql->execute();
                            $sl = 1;
                            while($row = $sql->fetch(PDO::FETCH_NUM)){
                                echo "<tr><td class='text-center'>".$sl."</td><td>".$row[0]."</td><td class='text-center'>".$row[1]."</td></tr>";
                                $sl++;
                            }
                        ?>
                    </table>
                    <?php
                        }else{
                    ?>
                    <h2>Requirements</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="input-group mb-3">
                        <select name="skill" class="form-select" required>
                            <option value="" selected disabled>Skill</option>
                            <?php
                                $sql = $conn->prepare("SELECT * FROM skillset");
                                $sql->execute();
                                while($row = $sql->fetch(PDO::FETCH_NUM)){
                                    echo "<option value=".$row[0].">".$row[1]."</option>";
                                }
                            ?>
                        </select>
                        <input type="text" name="business" class="form-control" placeholder="Business">
                        <input type="submit" name="requirement-submit" class="btn btn-success" value="Create">
                    </form>
                    <table class="table table-bordered table-striped table-hover mx-auto">
                        <tr>
                            <th class="text-center">Sl</th>
                            <th class="text-center">Skill</th>
                            <th class="text-center">Business</th>
                        </tr>
                        <?php
                            $sql = $conn->prepare("SELECT s.name, rq.business FROM requirements rq JOIN skillset s ON rq.skill = s.id WHERE rq.user = ?");
                            $sql->bindParam(1, $_SESSION["id"], PDO::PARAM_INT);
                            $sql->execute();
                            $sl = 1;
                            while($row = $sql->fetch(PDO::FETCH_NUM)){
                                echo "<tr><td class='text-center'>".$sl."</td><td>".$row[0]."</td><td class='text-center'>".$row[1]."</td></tr>";
                                $sl ++;
                            }
                        ?>
                    </table>
                    <?php
                        }
                    ?>
                </div>
            </section>
        </article>
    </main>
    <footer class="nav justify-content-around border-top sticky-bottom py-3 bg-white d-md-none">
        <a href="home" class="nav-link link-dark"><i class="fa-solid fa-house fa-xl"></i></a>
        <a href="search" class="nav-link link-dark"><i class="fa-solid fa-magnifying-glass fa-xl"></i></a>
        <a href="messages" class="nav-link link-dark"><i class="fa-regular fa-envelope fa-xl"></i></a>
        <a href="home" class="nav-link link-dark"><i class="fa-regular fa-bell fa-xl"></i></a>
    </footer>
</body>
<!-- Off Canvas -->
<aside id="aside-right" class="offcanvas offcanvas-end">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="list-group list-group-flush">
            <a href="profile" class="list-group-item list-group-item-action">Profile</a>
            <a href="setttings" class="list-group-item list-group-item-action">Settings</a>
            <a href="help" class="list-group-item list-group-item-action">Help</a>
            <a href="logout" class="list-group-item list-group-item-action">Logout</a>
        </div>
    </div>
</aside>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</html>