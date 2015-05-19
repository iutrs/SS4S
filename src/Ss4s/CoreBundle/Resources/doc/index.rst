.. ss4s documentation master file, created by
   sphinx-quickstart on Sun Feb  2 20:06:55 2014.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

.. toctree::
   :maxdepth: 3


CoreBundle
==========

il se trouve dans src/Ss4s/CoreBundle et contient tous les fichiers nécessaires au fonctionnement du coeur.

Service
-------

Ce sont les fichiers de services, déclarés dans src/Ss4s/CoreBundle/Resources/services.yml

ss4s.administrator_check
~~~~~~~~~~~~~~~~~~~~~~~~

Permet de vérifier le rôle de l'utilisateur en base de données.

**Méthodes** : 

* getAdministratorType :
	- paramètres : 
		+ string : $username, username de l'utilisateur qui doit être inspecté.
	- retour :
		+ array : contient le rôle de l'utilisateur.


ss4s.current_service
~~~~~~~~~~~~~~~~~~~~

Permet de savoir quel service l'utilisateur utilise afin d'avoir des paramètres voulus.

**Méthodes** :

* setCurrentService :
	- paramètres :
		+ Service ou null : $service, le service courrant.

* getArgs :
	-retour :
		+ array : un tableau qui contient les arguments du services (ceux ci sont à déclarer dans args.yml).

ss4s.ldap_check 
~~~~~~~~~~~~~~~

Permet d'interagir avec la base données LDAP indiquée en paramètre du service.

**Méthodes** :

* getInfos : 
	- paramètres :
		+ string : $username, l'utilisateur dont on veut obtenir les informations.
	- retour : 
		+ array : retourne un tableau qui contient une chaîne 'fullname', nom complet de l'utilisateur et un tableau 'groups', qui contient les groupes scolaires de l'utilisateur. 

* getFullname :
	- paramètres : 
		+ string : $username, l'utilisateur dont on veut obtenir le nom.
	- retour : 
		+ string : nom complet de l'utilisateur recherché.

* getGroups :
	- paramètres :
		+ string : $username, l'utilisateur dont on veut obtenir les groupes.
	- retour :
		+ array : les groupes de l'utilisateur (ex: ILL_student).

* getUsersLike :
	- paramètres : 
		+ string : $expression, la chaine de début du nom complet de l'utilisateur recherché (au format NOM prénom).
		+ int : $limit, la limite de recherche.
	- retour : 
		+ array ou null : tableau à deux dimensions avec pour chaque user : ['username'] et ['fullname'].

ss4s.plugins_finder
~~~~~~~~~~~~~~~~~~~

Service qui permet de retrouver les Controller dans le dossier des plugins et donc les services qui existent.

**Méthode** :

* getExistingServices :
	- retour : les services (donc plugins) disponibles.

Ss4sLogs.php et Ss4sLogsProcessor.php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Ces fichiers sont nécessaires au fonctionnement des logs. Veuillez ne pas les modifier.

Plugins
=======

Les plugins de Ss4s sont en fait des bundles. Pour en génerer un, il vous suffit donc en ligne de commande de taper, dans le dossier racine de l'application :

**php** app/console generate:bundle

Et de suivre les instructions. Les bundles plugins doivent être dans src/Ss4s/Plugins/, donc il faudra utiliser le namespace : 

.. code-block:: php
	
	Ss4s/Plugins/VotrePluginBundle


Puis mettre à jour app/AppKernel.php en rajoutant votre plugin à la liste des bundles. Enfin, ajoutez la route de base de votre plugin dans app/config/routing.yml.

Les Controller
--------------

Il est très important qu'il n'y ait qu'un seul Controller dans vos plugins et que celui-ci implémentente l'interface Ss4s\CoreBundle\Controller\Service\PluginContoller. C'est cette interface qui permet de gérer l'accès au service.

.. code-block:: php

	<?php

	use Ss4s\CoreBundle\Controller\Service\PluginContoller

	class VotreController implements PluginController 
	{
		...
	}

Les arguments du service
------------------------
Il est très important d'avoir un fichier Resources/config/args.yml dans votre plugin. Celui-ci contiendra les arguments de votre plugin. Les arguments peuvent être n'importe quel paramètre que vous jugez utile pour utiliser plusieurs fois de manière différente un même plugin. 

Pour récupérer ces paramètres dans le controller, il vous suffit de faire appel au service ss4s.current_service :

.. code-block:: php 
	
	<?php

	$args = $this->get('ss4s.current_service')->getArgs();

Vous pouvez parcourir ces arguments comme un tableau associatif.

Les views 
---------
La vue de base est la vue standard de Symfony, pour l'implémenter :

.. code-block:: html
	
	{% extends '::base.html.twig' %}

block title
~~~~~~~~~~~
Le titre de votre page ("Ss4s -" y est déjà intégré) :

.. code-block:: html 

	{% block title %}
		{{ parent() }} Votre titre
	{% endblock %}

block stylesheets
~~~~~~~~~~~~~~~~~
Ce block vous permet de rajouter des feuilles de style.

block header 
~~~~~~~~~~~~
block du haut de page, prévu pour accueillir un grand titre.

block content 
~~~~~~~~~~~~~
Ce block vous permet de mettre en place de contenu principal de la page. Nous vous recommandons, dans un soucis d'uniformité, de rajouter les blocs html suivants : 

.. code-block:: html

	{% block content %}
	<div class="custom-module">
		<div class="custom-module-header">
			<div class="custom-mini-icon">
				<span class="glyphicon <!-- une glyphicon de bootstrap -->"></span>
			</div>
			<h3>
				<!-- Le titre de cette partie du plugin -->
			</h3>
		</div>
		<div class="well">
			<!-- Votre contenu -->
		</div>
	</div>
	{% endblock %}

block script 
~~~~~~~~~~~~
Ajoutez dans ce block vos scripts personnalisés propres à chaque page.