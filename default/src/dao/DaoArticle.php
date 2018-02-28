<?php

namespace simplon\dao;
use simplon\entities\Article;
use simplon\dao\Connect;

class DaoArticle {
    public function getAll($id):array {

        $tab = [];
 
        try {
           
            $query = Connect::getInstance()->prepare('SELECT * FROM article WHERE user_id = :id');
            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            
            $query->execute();
           
            while($row = $query->fetch()) {
               
                $art = new Article($row['title'], 
                            $row['content'],
                            $row['id']);
                $tab[] = $art;
            }
        }catch(\PDOException $e) {
            echo $e;
        }
        return $tab;
    }
    public function getById(int $id) {
        
        try {
           
            $query = Connect::getInstance()->prepare('SELECT * FROM article WHERE id=:id');
            
            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            $query->execute();
            if($row = $query->fetch()) {
                $art = new Article($row['title'], 
                            $row['content'],
                            $row['id']);
                return $art;
            }
        }catch(\PDOException $e) {
            echo $e;
        }
      
        return null;
    }
    
    public function add(Article $art, $id) {
        
        try {
            //On prépare notre requête, avec les divers placeholders
            $query = Connect::getInstance()->prepare('INSERT INTO article (title,user_id,content) 
            VALUES (:title, :user_id, :content)');
           
            $query->bindValue(':title',$art->getTitle(),\PDO::PARAM_STR);
            $query->bindValue(':user_id',$id,\PDO::PARAM_INT);
            $query->bindValue(':content',$art->getContent(),\PDO::PARAM_STR);
            $query->execute();
            /**
             * On fait en sorte de récupérer le dernier id généré par SQL 
             * afin de l'assigner à l'id de notre instance de Article
             */
            $art->setId(Connect::getInstance()->lastInsertId());
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }
   
    public function update(Article $art) {
        
        try {
            $query = Connect::getInstance()->prepare('UPDATE Article SET title = :title, content = :content WHERE id = :id');
            $query->bindValue(':title',$art->getTitle(),\PDO::PARAM_STR);
            $query->bindValue(':content',$art->getContent(),
            \PDO::PARAM_INT);
            $query->bindValue(':id',$art->getId(),
            \PDO::PARAM_INT);

            $query->execute();
            
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }

    public function delete(Article $id) {
        
        try {
            $query = Connect::getInstance()->prepare('DELETE FROM article WHERE id = :id');
            $query->bindValue(':id',$id,\PDO::PARAM_INT);
            $query->execute();
            
        }catch(\PDOException $e) {
            echo $e;
        }
    }


}
