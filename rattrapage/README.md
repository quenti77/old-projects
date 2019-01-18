Projet de rattrapage (WIKI)
===========================

Introduction
------------
Voici le dépo officiel du projet de rattrapage pour ma troisième année d'école.

Le site web
-----------
Le site web du projet permet de gérer ses plugins de manière simple grâce à une interface intuitives.
Le site web est disponible à cette adresse : [Le site web](http://quenti77.fr/rattrapage/ "Le site web")

Vous pourrez d'ici :
- Rajouter des plugins créer par d'autres utilisateur
- Demander de les supprimer si vous n'en voulez plus
- Demander de les mettres à jour si besoin
- Rajouter ses propes plugins
- Modifier les informations de ses plugins

Bien sur pour pouvoir utiliser le site web ou même le logiciel il faudra vous inscrire.
Voilà je pense que c'est tout pour le site web

Le logiciel
-----------

Le logiciel est à la base une coquille vide qui ne fait rien de plus que prendre des plugins pour les gréffer dans son interface. Les plugins peuvent faire des action simple comme gérer un timer ou des choses plus complexe comme lire des message depuis un tchat IRC par exemple. Une seule limite l'imagination du développeur !

Vous pouvez d'ors et déjà télécharger l'application pour la tester !
+ Pour la version Windows : [Rattrapage.exe](http://quenti77.fr/rattrapage/bdd/Rattrapage.exe)
+ Pour la version Linux / Mac : [Rattrapage.jar](http://quenti77.fr/rattrapage/bdd/Rattrapage.jar)

N'oubliez pas de vous inscrire sur le site et de rajouter des plugins !

Tutoriel : création d'un plugin
-------------------------------

Pour créer un nouveau plugin, il faut connaitre le java de manière général. C'est-à-dire la partie Orienté Objet surtout et si besoin la communication avec une base de donnée via un SGBD comme MySQL par exemple.

Ce tutoriel vous expliquera comment créer un plugin fonctionnel qui sera lancé dans l'interface. Il a été réalisé avec eclipse mais peut très bien être adapté à votre environnement de travail.

Tout d'abord il vous faut l'API du logiciel qui permet d'avoir les classes de base pour pouvoir faire un plugin compréhensible par l'application. Il se trouve ici : [PluginAPI.jar](http://quenti77.fr/rattrapage/bdd/PluginAPI.jar)

Ensuite il vous faut créer un nouveau projet eclipse et importer se plugin à ce projet. Une fois l'API chargé vous devez créer une classe qui implémentera l'interface IPlugin du package *fr.quenti.plugins*.

Cela rajoutera 2 méthodes obligatoire qui sont :

1. __getInformation()__
2. __getInterface()__

La première permet de connaitre les informations sur le plugin. C'est-à-dire le nom, la version et l'auteur.
La seconde méthode permet de retourner votre interface IHM créé dans un panel. Ce panel sera rajouté à l'interface dans un onglet à part. Cette méthode reçoit en paramètre un JMenuBar qui correspond à la bar de menu de l'application principale.

Voici le code de base que normalement un plugin devrait avoir :

```java
package fr.ircp.plugin;

import java.awt.BorderLayout;

import javax.swing.JMenuBar;
import javax.swing.JPanel;

import fr.quenti.plugins.IPlugin;
import fr.quenti.plugins.InformationPlugin;

/**
 * Permet de lier le plugin à l'application.
 * @author quenti77
 * @version 1.0.0.0
 */
public class InternetRelayChatPlugin implements IPlugin {

	/**
	 * Information de base de tout plugin.
	 */
	private InformationPlugin informationPlugin = null;
	private JPanel content = null;
	
	@Override
	public InformationPlugin getInformation() {
		if (this.informationPlugin == null) {
			this.informationPlugin = new InformationPlugin("IRCP", "quenti77", "1.0.0.0");
		}
		
		return this.informationPlugin;
	}

	@Override
	public JPanel getInterface(JMenuBar arg0) {
		if (this.content == null) {
			// Une méthode pour créer les éléments du menu
			// Une méthode pour créer les élements de l'interface visuelle
			
			this.content = new JPanel( new BorderLayout() );
		}
		
		return this.content;
	}
	
}
```

Conclusion
----------

Ce projet de rattrapage bien que simple à première vue est plus complexe que je le pensais. Surtout la partie chargement des plugins car il y a plusieurs cas possible qu'ils faut traiter.
