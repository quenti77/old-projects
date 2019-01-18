package fr.quenti.application;

import javax.swing.SwingUtilities;

import fr.quenti.forms.ConnectionForm;

/**
 * Classe de base de l'application.
 * Contient la fonction main pour lancer l'application
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class Application {
	
	/**
	 * Point d'entrée de l'application
	 * @param args
	 */
	public static void main( String[] args) {
		
		SwingUtilities.invokeLater(new Runnable() {

			/**
			 * Méthode run permettant de lancer l'application dans
			 * un processus diférent.
			 */
			@Override
			public void run() {
				ConnectionForm frmConnection = new ConnectionForm();
				frmConnection.setVisible(true);
			}
			
		});
	}
	
}
