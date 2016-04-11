<?php
(empty($header['logo'])?$logo = "/img/logo.png":$logo = $header['logo']);
 ?>
<body>
<header id="siteHeader">
<?php drawLang($file_db,$translation,$lang); ?>
  <nav id="logo" class="flex-row-fluid flex-center wrapper pad2">
    <a href="/<?=$lang?>/" class="block"><img src="<?=$logo?>" id="logoImg" alt="logo" class="flex0"></a>
    <div class="flex1"></div>
  </nav>
  <div class="black-bg">
    <nav id="menu" class="flex-row-fluid flex-center wrapper">
      <?php
      (empty($_GET['cat'])?$activation = " active":$activation = ""); ?>
  	<a href="/<?=$lang?>/" class="home block pushState flex0<?=$activation?>" data-title="Chaire"></a>
  <?php
      $result = $file_db->prepare('SELECT cl.id_cat,name,lang,image,c.priority FROM category_lang cl JOIN category c ON c.id_cat = cl.id_cat WHERE lang LIKE :lang ORDER BY c.priority ASC');
      $result->bindParam(":lang",$lang);
      $result->execute() or die('AHAH');
      foreach($result as $row){
        (cleanString($row['name']) == $_GET['cat']? $class = " active": $class = "");
        echo('    <a href="/'.strtolower($row['lang']).'/'.cleanString($row['name']).'" class="cat'.$row['id_cat'].' block pushState flex0'.$class.'" data-priority="'.$row['priority'].'">'.$row['name'].'</a>'."\n");
      }
      if(isLogedNC()){
        ?>    <a class="block pushState flex0 admin" id="newCat"><?=$translation['admin_newCat']?></a>
  <?php
      }
  ?>
    </nav>
  </div>
</header>
