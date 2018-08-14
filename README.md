- the database url location is : `mysql://root:root@127.0.0.1:3306/user`

- to change the file folder you must edit the file config/services.yml
  * import-file-path: is the file directory
  * import-file-name: is the file name
  * import-file-extension: is the file extension
 
-  to inject the file testtakers.csv or testtakers.json you must open a terminal, go in project folder and execute this command :
`bin/console app:create-user `

- to start the server you must open a terminal, go in project folder and execute this command : `php bin/console server:run`

- to ask the api rest you can open postman and call this url
  * `http://127.0.0.1:8000/users` => will return all users from the database
  * `http://127.0.0.1:8000/user/id/{id}` => replace {id} with the id from the user that you would have
  * `http://127.0.0.1:8000/users?limit=10&offset=1&filter=myers` => will return all users from the database : 
    * limit : number of items to display
    * offset : index of beginning of pagination
    * filter : firstname or lastname research filter
