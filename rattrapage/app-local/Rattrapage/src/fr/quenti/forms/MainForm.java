package fr.quenti.forms;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.ArrayList;

import javax.swing.JFrame;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTabbedPane;

import fr.quenti.model.PluginListModel;
import fr.quenti.plugins.IPlugin;
import fr.quenti.plugins.InformationPlugin;
import fr.quenti.tools.PluginsLoader;
import fr.quenti.users.Account;

/**
 * Fenêtre principale de l'application
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class MainForm extends JFrame implements ActionListener {

	private static final long serialVersionUID = 2L;

	private Account account = null;
	private ConnectionForm connect = null;
	private PluginListModel modelTable = null;
	private PluginsLoader loader = null;
	
	/**
	 * 
	 * @param account : Le compte connecté
	 * @param connectForm : Le formulaire de connexion
	 * @param modelTable : La liste des plugins
	 */
	public MainForm(Account account, ConnectionForm connectForm, PluginListModel modelTable) {
		this.account = account;
		this.connect = connectForm;
		this.modelTable = modelTable;
		
		try {
			this.loader = new PluginsLoader();
		} catch (Exception e) {
			JOptionPane.showMessageDialog(null, "Erreur lors du chargement des plugins", "MainForm constructor", JOptionPane.ERROR_MESSAGE);
		}
		
		this.createMenu();
		this.createIHM();
		this.pack();
		this.setMinimumSize(this.getSize());
		
		this.setTitle("Rattrapage");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	}

	/**
	 * 
	 * @return le compte de l'utilisateur
	 */
	public Account getAccount() {
		return account;
	}

	/**
	 * 
	 * @return la fenêtre de connexion
	 */
	public ConnectionForm getConnect() {
		return connect;
	}

	/**
	 * 
	 * @return la liste des plugins
	 */
	public PluginListModel getModelTable() {
		return modelTable;
	}
	
	/**
	 * Permet d'ajouter un sous menu
	 * @param name : le nom du menu pour l'élément 'click'
	 * @param content : Le texte du menu
	 * @return l'élément de sous menu
	 */
	private JMenuItem addSubMenu(String name, String content) {
		JMenuItem item = new JMenuItem(content);
		
		item.setName(name);
		item.addActionListener(this);
		
		return item;
	}
	
	/**
	 * Ajoute un menu dans la barre de menu principale
	 * @param name : Nom du menu dans le code (Pour l'événement click)
	 * @param content : Nom du menu affiché
	 * @param items : Liste des sous-menu
	 * @return le menu à envoyer en JMenuBar
	 */
	private JMenu addMenu(String name, String content, JMenuItem[] items) {
		JMenu menu = new JMenu(content);
		
		menu.setName(name);
		menu.addActionListener(this);
		
		for (JMenuItem item : items) {
			menu.add(item);
		}
		
		return menu;
	}
	
	/**
	 * Création du menu principale
	 */
	private void createMenu() {
		JMenuBar mainMenu = new JMenuBar();
		
		/* Menu fichier */
		mainMenu.add(addMenu("mnuFile", "Fichier", new JMenuItem[] {
			addSubMenu("mnuFileLogout", "Se déconnecter"),
			addSubMenu("mnuFileRestart", "Redémarrer ..."),
			addSubMenu("mnuFileQuit", "Quitter ...")
		}));
		
		this.setJMenuBar(mainMenu);
	}
	
	/**
	 * Création de l'interface pour l'utilisateur
	 */
	private void createIHM() {
		
		Dimension d = new Dimension(600, 400);
		
		JPanel content = new JPanel(new BorderLayout());
		content.setPreferredSize(d);
		content.setSize(d);
		
		JTabbedPane tab = new JTabbedPane(JTabbedPane.BOTTOM);
		
		if (loader != null) {
			try {
				ArrayList<IPlugin> list = loader.getPlugins(this.account.getUserName());
				int i = 0;
				
				for (IPlugin plugin : list) {
					InformationPlugin infoPlug = plugin.getInformation(this.account);
					
					JPanel panelMainTab = new JPanel(new BorderLayout());
					Component pan = plugin.getInterface(this.getJMenuBar());
					panelMainTab.add(pan, BorderLayout.CENTER);
					
					tab.add(infoPlug.getName(), panelMainTab);
					tab.setToolTipTextAt(i++, infoPlug.getAuthor() + " - " + infoPlug.getVersion());
				}
				
			} catch (Exception e) {
				JOptionPane.showMessageDialog(null, "Erreur de chargement des plugins\n" + e.getMessage(), "MainForm createIHM", JOptionPane.ERROR_MESSAGE);
			}
		} else {
			JOptionPane.showMessageDialog(null, "Erreur de chargement des plugins !", "Fin", JOptionPane.ERROR_MESSAGE);
		}
		
		content.add(tab, BorderLayout.CENTER);
		
		this.getContentPane().add(content);
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
		if (arg0.getSource() instanceof JMenuItem) {
			String name = ((JMenuItem) arg0.getSource()).getName();
			
			if (name.equalsIgnoreCase("mnuFileLogout")) {
				this.setVisible(false);
				connect.reload(null);
			} else if (name.equalsIgnoreCase("mnuFileRestart")) {
				this.setVisible(false);
				connect.reload(account);
			} else if (name.equalsIgnoreCase("mnuFileQuit")) {
				System.exit(0);
			}
		}
	}
	
}