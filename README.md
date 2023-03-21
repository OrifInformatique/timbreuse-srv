# timbreuse web interface
* https://github.com/OrifInformatique/ci_packbase_v4  
* https://github.com/OrifInformatique/Timbreuse  

## à faire
* [ ] rajouter un jeton de vérification losqu’on get les logs
* [x] déplacer les méthodes api de Logs.php dans LogsAPI.php
* [ ] mise à jour codeigniter
* [ ] mise à jour bootstrape
* [x] ajouter bouton pour modifier un log existant
* [ ] corriger le bouton annuler quand on confirme la suppresion
* [x] corriger l’astérisque sur la vue jour
* [ ] modifier une attribution de de badge
* [ ] soft deleted un utilisateur de timbreuse

## bug
* [ ] si la modification est uniquement l’entré ou sortir, il n’y aura pas 
d’indication de modification
* [ ] si un log timbrage est fait alors qui a un log site qui est déjà sur le
même datetime. Le serveur ne prendra pas le log et le log ne sera pas effacer
de la table log_write
* [ ] l’astérisque s’affiche pas pour les suppretion de log

## chose à modifier pour upgrade codeIgniter
* poste et validation, utiliser validateData et is('post')
* route et les liens relatifs différent
* ~ time dif retour des valeurs négativee
