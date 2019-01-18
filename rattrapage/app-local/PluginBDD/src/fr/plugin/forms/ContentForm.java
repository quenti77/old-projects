package fr.plugin.forms;

import java.awt.BorderLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

import javax.swing.JButton;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;

import fr.plugin.application.BasePlugin;
import fr.plugin.data.Task;
import fr.plugin.data.TaskListModel;
import fr.plugin.tools.SingleBDD;

public class ContentForm extends JPanel implements ActionListener {

	private static final long serialVersionUID = 10L;
	
	private JTable table = null;
	private JButton btnRefresh = null;
	private TaskListModel modelTable = null;
	private BasePlugin basePlugin = null;
	
	/**
	 * Constructeur pour le composant.
	 * @param menu : La barre de menu.
	 */
	public ContentForm(JMenuBar menu, BasePlugin plugin) {
		
		this.basePlugin = plugin;
		
		// Menu : gestion
		JMenu mnuGestion = new JMenu("Gestion de tâches");
		mnuGestion.addActionListener(this);
		mnuGestion.setName("mnuGestion");
		
		// Item gestion : Créer nouvelle tâche
		JMenuItem mnuGestionCreate = new JMenuItem("Création d'une tâche");
		mnuGestionCreate.addActionListener(this);
		mnuGestionCreate.setName("mnuGestionCreate");
		
		// Item gestion : recharger
		JMenuItem mnuGestionRefresh = new JMenuItem("Rechargement des tâches");
		mnuGestionRefresh.addActionListener(this);
		mnuGestionRefresh.setName("mnuGestionRefresh");
		
		mnuGestion.add(mnuGestionCreate);
		mnuGestion.add(mnuGestionRefresh);
		
		menu.add(mnuGestion);
		
		this.createIHM();
	}

	/**
	 * Permet la création de l'interface à afficher
	 */
	private void createIHM() {
		
		// Model
		updateModel();
				
		// Tableau de récap
		table = new JTable(modelTable);
		
		// Bouton de rafraichissement
		btnRefresh = new JButton("Rafraichir les tâches ...");
		btnRefresh.setName("btnRefresh");
		btnRefresh.addActionListener(this);
		
		this.setLayout(new BorderLayout());
		
		this.add(btnRefresh, BorderLayout.SOUTH);
		this.add(new JScrollPane(table), BorderLayout.CENTER);
	}
	
	private void updateModel() {
		modelTable = new TaskListModel();
		
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("SELECT t_id, t_name, t_desc, t_author, t_check FROM task WHERE t_author = ?");
			prepare.setString(1, this.basePlugin.getAccount().getUserName());
			
			ResultSet result = prepare.executeQuery();
			while (result.next()) {
				
				modelTable.addTasks(new Task(result.getInt("t_id"),
											 result.getString("t_name"),
											 result.getString("t_desc"),
											 result.getString("t_author"),
											 result.getInt("t_check") == 1 ));
			}
			
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
	public void actionPerformed(ActionEvent e) {
		
		if (e.getSource() instanceof JButton) {
			updateModel();
			
			table.setModel(modelTable);
		} else if (e.getSource() instanceof JMenuItem) {
			JMenuItem tmpMenu = (JMenuItem) e.getSource();
			
			if ("mnuGestionRefresh".equalsIgnoreCase(tmpMenu.getName())) {
				updateModel();
				
				table.setModel(modelTable);
			}
			
			if ("mnuGestionCreate".equalsIgnoreCase(tmpMenu.getName())) {
				ConnectionForm frmAdd = new ConnectionForm(this.basePlugin.getAccount());
				frmAdd.setVisible(true);
			}
		}
	}
}
