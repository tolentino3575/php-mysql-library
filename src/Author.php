<?php

    class Author
    {
        private $name;
        private $id;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        function getName()
        {
            return $this->name;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $query = $GLOBALS['DB']->query("SELECT * FROM authors WHERE name = '{$this->getName()}'");
            $returned_author = $query->fetchAll(PDO::FETCH_ASSOC);
            $found_author = null;

            foreach($returned_author as $author) {
              $author_id = $author['id'];
              $found_author = Author::find($author_id);
            }

            if ($found_author != null) {
              return $found_author;
            } else {
              $GLOBALS['DB']->exec("INSERT INTO authors (name) VALUES ('{$this->getName()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
            }

        }

        static function getAll()
        {
            $returned_authors = $GLOBALS['DB']->query("SELECT * FROM authors;");
            $authors = array();
            foreach($returned_authors as $author) {
                $title = $author['name'];
                $id = $author['id'];
                $new_author = new Author($title, $id);
                array_push($authors, $new_author);
            }
            return $authors;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM authors");
        }

        static function find($id)
        {
            $all_authors = Author::getAll();
            $found_author = null;
            foreach ($all_authors as $author) {
                $author_id = $author->getId();
                if ($author_id == $id) {
                    $found_author = $author;
                }
            }
            return $found_author;
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM authors WHERE id = {$this->getId()};");
        }

        function update($new_author)
        {
            $GLOBALS['DB']->exec("UPDATE authors SET name = {$new_author} WHERE id = {$this->getId()};");
            $this->setName($new_author);
        }

        static function search($search_author)
        {
            $all_authors = Author::getAll();
            $lowercase_search = strtolower($search_author);
            $found_authors = array();
            foreach($all_authors as $author) {
                $lowercase_author = strtolower($author->getName());
                $compare = strpos($lowercase_author, $lowercase_search);
                if( is_numeric($compare)) {
                    array_push($found_authors, $author);
                }
            }
            return $found_authors;
        }

        function addBook($book)
        {
            $GLOBALS['DB']->exec("INSERT INTO books_authors (books_id, authors_id) VALUES ({$book->getId()}, {$this->getId()});");
        }

        function getBooks()
        {
            $query = $GLOBALS['DB']->query("SELECT books.* FROM authors
                JOIN books_authors ON (authors.id = books_authors.authors_id)
                JOIN books ON (books_authors.books_id = books.id)
                WHERE authors.id = {$this->getId()}; ");

            $returned_books = $query->fetchAll(PDO::FETCH_ASSOC);
            $books = array();
            foreach($returned_books as $book){
                $title = $book['title'];
                $id = $book['id'];
                $new_book = new Book($title, $id);
                array_push($books, $new_book);
            }
            return $books;
        }

    }



?>
