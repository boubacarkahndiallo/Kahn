Pour pouvoir géré la deconnexion après authantification, il faut crée un autre controller par exemple deconnexion ou logout
après le géré dans route, de type get
----
<!-- Pour sécurisé une route: -->
on utilise la fonction middleware Exemple: ->middleware('auth')

Token : un url inique pour chaque utilisateur
