package fr.quenti.forms;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.ResultSet;
import java.sql.SQLException;

import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;

import com.mysql.jdbc.PreparedStatement;

import fr.quenti.model.Plugin;
import fr.quenti.model.PluginListModel;
import fr.quenti.tools.SingleBDD;
import fr.quenti.users.Account;

/**
 * Fenêtre de confirmation des actions sur les plugins
 * @author quenti77
 * @version 0.0.0.1
 */
public class UpdateConfirmForm extends JFrame implements ActionListener {

	private static final long serialVersionUID = 3L;
	
	private Account account = null;
	private ConnectionForm connect = null;
	
	private PluginListModel modelTable = null;
	private JButton btnConfirm = null;
	private JButton btnRefresh = null;
	private JTable table = null;
	
	/**
	 * Permet de connêtre la fenêtre de connexion.
	 * @return la fenêtre de connexion
	 */
	public ConnectionForm getConnectionForm() {
		return this.connect;
	}
	
	public UpdateConfirmForm(Account account, ConnectionForm connectForm) {
		this.account = account;
		this.connect = connectForm;
		
		this.updateLast();
		
		this.createIHM();
		this.pack();
		this.setMinimumSize(this.getSize());
		
		this.setTitle("Confirmation ...");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	}
	
	/**
	 * L'utilisateur viens de se connecter.
	 * On mets à jour sa date de dernière connexion.
	 */
	private void updateLast() {
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("UPDATE users SET u_last = NOW() WHERE u_id = ?");
			prepare.setInt(1, account.getId());
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
	 * Récupération de la liste de tous les plugins
	 * de l'utilisateur.
	 */
	private void updateModel() {
		modelTable = new PluginListModel();
		
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("SELECT pl_id, pl_version, pl_action, p_name, p_version, p_link FROM plugin_list LEFT JOIN plugins ON plugin_list.pl_pluginid = plugins.p_id WHERE pl_userid = ?");
			prepare.setInt(1, account.getId());
			
			ResultSet result = prepare.executeQuery();
			while (result.next()) {
				Integer actionResult = result.getInt("pl_action");
				
				modelTable.addPlugins(new Plugin(result.getInt("pl_id"),
												 result.getString("p_name"),
												 result.getString("pl_version"), actionResult,
												 result.getString("p_link"),
												 result.getString("p_version")));
			}
			
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
	 * Création de l'interface pour l'utilisateur
	 */
	private void createIHM() {
		
		JPanel content = new JPanel(new BorderLayout());
		
		// Model
		updateModel();
		
		// Tableau de récap
		table = new JTable(modelTable);
		table.setMinimumSize(new Dimension(250, 250));
		
		// Panel Button
		JPanel actionButton = new JPanel(new GridLayout(1, 2));
		
		// Bouton de confirmation
		btnConfirm = new JButton("Confirmer et continuer ...");
		btnConfirm.setName("btnConfirm");
		btnConfirm.setPreferredSize(new Dimension(125, 35));
		btnConfirm.addActionListener(this);
		
		// Bouton de confirmation
		btnRefresh = new JButton("Rafraichir ...");
		btnRefresh.setName("btnRefresh");
		btnRefresh.setPreferredSize(new Dimension(125, 35));
		btnRefresh.addActionListener(this);
		
		actionButton.add(btnRefresh);
		actionButton.add(btnConfirm);
		
		content.add(actionButton, BorderLayout.SOUTH);
		content.add(new JScrollPane(table), BorderLayout.CENTER);
		
		this.getContentPane().add(content);
	}

	@Override
	public void actionPerformed(ActionEvent e) {
		if (e.getSource() instanceof JButton) {
			JButton btn = (JButton) e.getSource();
			
			if (btn.getName().equals("btnRefresh")) {
				// Refresh
				updateModel();
				table.setModel(modelTable);
			} else if (btn.getName().equals("btnConfirm")) {
				// Confirmation
				this.setVisible(false);
				
				UpdateForm frmUpdate = new UpdateForm(account, connect, modelTable);
				frmUpdate.setVisible(true);
			}
		}
	}

}
