package fr.quenti.thread;

import java.io.File;
import java.io.IOException;
import java.sql.SQLException;

import javax.swing.JOptionPane;

import com.mysql.jdbc.PreparedStatement;

import fr.quenti.forms.UpdateForm;
import fr.quenti.model.Plugin;
import fr.quenti.model.PluginListModel;
import fr.quenti.tools.PluginsLoader;
import fr.quenti.tools.SingleBDD;
import fr.quenti.tools.Transfer;
import fr.quenti.users.Account;

/**
 * 
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class ThreadUpdate extends Thread {

	private UpdateForm frmUpdate = null;
	private PluginListModel model = null;
	private Account account = null;
	
	public ThreadUpdate(UpdateForm frmUpdate, PluginListModel model, Account account) {
		this.frmUpdate = frmUpdate;
		this.model = model;
		this.account = account;
		
		File pluginsFolder = PluginsLoader.getPluginsPath(this.account.getUserName());
		
		if (pluginsFolder.isFile()) {
			pluginsFolder.delete();
		}
		
		if (!pluginsFolder.exists()) {
			pluginsFolder.mkdirs();
		}
	}
	
	/**
	 * Mets à jour l'action effectué et/ou la version du plugin dans la BDD
	 * @param version : Nouvelle version du plugin
	 * @param action : Nouvelle action du plugin
	 * @param id : Id du plugin à modifier
	 */
	public void update(String version, int action, int id) {
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("UPDATE plugin_list SET pl_version = ?, pl_action = ? WHERE pl_id = ?");
			prepare.setString(1, version);
			prepare.setInt(2, action);
			prepare.setInt(3, id);
			
			prepare.executeUpdate();
			
		} catch (Exception e) {
			JOptionPane.showMessageDialog(null, "La connexion à la base de donnée a échoué !", "Erreur de connexion", JOptionPane.ERROR_MESSAGE);
			System.exit(1);
		} finally {
			if (prepare != null) {
				try {
					prepare.close();
				} catch (SQLException e) {
					System.exit(1);
				}
			}
		}
	}
	
	/**
	 * Suppression du plugin dans la table jointure.
	 * @param id
	 */
	public void delete(int id) {
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("DELETE FROM plugin_list WHERE pl_id = ?");
			prepare.setInt(1, id);
			
			prepare.executeUpdate();
		} catch (Exception e) {
			JOptionPane.showMessageDialog(null, "La connexion à la base de donnée a échoué !\n" + e.getMessage(), "Erreur de connexion", JOptionPane.ERROR_MESSAGE);
			System.exit(1);
		} finally {
			if (prepare != null) {
				try {
					prepare.close();
				} catch (SQLException e) {
					System.exit(1);
				}
			}
		}
	}
	
	@Override
	public void run() {
		if (model != null && model.getRowCount() > 0) {
			Plugin plugin = null;
			int action = -1;
			int idPlugin = 0;
			
			/**
			 * Boucle chaque plugin
			 */
			for (int i = 0; i < model.getRowCount(); i += 1) {
				
				plugin = model.getPlugin(i);
				action = plugin.getAction();
				idPlugin = plugin.getId();
				
				String link = plugin.getLink();
				
				if (link.substring(0, 8).equals("plugins/")) {
					link = link.substring(8);
				}
				
				File pluginsFile = new File(PluginsLoader.getPluginsPath(this.account.getUserName()) + File.separator +  link);
				
				if (action == 0) {
					frmUpdate.setStatus("Plugin " + plugin.getName() + " : check ...", (int)( ((float)(i) /  (float)(model.getRowCount()) ) * 100 ));
					if (!pluginsFile.exists()) {
						action = 1;
					}
				}
				
				if (action != 0) {
					if (pluginsFile.exists()) {
						pluginsFile.delete();
					}
				}
				
				if (action == 4 || action == 1) {
					frmUpdate.setStatus("Plugin " + plugin.getName() + " : téléchargement ...", (int)( ((float)(i) /  (float)(model.getRowCount()) ) * 100 ));
					int tryUpdate = 0;
					boolean finis = false;
					
					for (tryUpdate = 0; tryUpdate < 5 && finis == false; tryUpdate += 1) {
						try {
							Transfer.downloadFile("http://quenti77.fr/rattrapage/" + plugin.getLink(), pluginsFile);
							finis = true;
						} catch (IOException e) {
							try {
								Thread.sleep(1000);
							} catch (InterruptedException ex) {
								// Empty
							}
						}
					}
					
					if (finis) {
						update(plugin.getVersionOff(), 0, idPlugin);
					}
				} else if (action == 2) {
					frmUpdate.setStatus("Plugin " + plugin.getName() + " : suppresion ...", (int)( ((float)(i) /  (float)(model.getRowCount()) ) * 100 ) );
					delete(idPlugin);
				}
				
				try {
					Thread.sleep(500);
				} catch (InterruptedException e) {
					// Empty
				}
				
				frmUpdate.setStatus("Plugin " + plugin.getName() + " : terminé !", (int)( ((float)(i + 1) /  (float)(model.getRowCount()) ) * 100 ));
			}
		}
		
		frmUpdate.setStatus("Finis", -1);
	}

	
	
}
