package fr.quenti.plugins;

import java.awt.Component;

import javax.swing.JMenuBar;

import fr.quenti.users.Account;

/**
 * Interface de base pour tous les plugins
 * @author quenti
 * @version 0.0.0.1-dev
 */
public interface IPlugin {

	/**
	 * Cette méthode retourne les informations du plugin
	 * @return InformationPlugin : les informations du plugin
	 */
	public InformationPlugin getInformation(Account account);
	
	/**
	 * Permet au plugin d'initialiser son interface IHM
	 * @param arg0 : Permet l'accès à la base de menu
	 * @return JPanel : Retourne l'interface IHM du plugin
	 */
	public Component getInterface(JMenuBar arg0);
	
}
