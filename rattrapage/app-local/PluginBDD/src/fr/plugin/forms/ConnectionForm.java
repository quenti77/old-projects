package fr.plugin.forms;

import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.SQLException;

import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;

import com.mysql.jdbc.PreparedStatement;

import fr.plugin.tools.SingleBDD;
import fr.quenti.users.Account;

/**
 * Fenêtre de connexion qui permet à l'utilisateur de s'authentifier
 * @author quenti77
 * @version 0.0.0.1-dev1
 */
public class ConnectionForm extends JFrame implements ActionListener {

	/**
	 * Serial de la fenêtre
	 */
	private static final long serialVersionUID = 1L;
	
	private JTextField txtName = null;
	private JTextField txtDesc = null;
	private JButton btnCancel = null;
	private JButton btnSubmit = null;
	private Account account = null;
	
	/**
	 * Constructeur de la classe ConnectionForm
	 */
	public ConnectionForm(Account account) {
		this.account = account;
		this.createIHM();
		this.setSize(new Dimension(400, 150));
		this.setResizable(false);
		
		this.setTitle("Ajout d'une tâche ...");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	}
	
	/**
	 * Méthode de création de la fenêtre
	 */
	private void createIHM() {
		JLabel lblPseudo = new JLabel("Nom de la tâche : ");
		JLabel lblPassword = new JLabel("Description : ");
		
		this.txtName = new JTextField();
		this.txtName.setPreferredSize(new Dimension(200, 50));
		
		this.txtDesc = new JTextField();
		this.txtDesc.setPreferredSize(new Dimension(200, 50));
		
		this.btnSubmit = new JButton("Nouvelle tâche ...");
		this.btnSubmit.setName("btnSubmit");
		this.btnSubmit.setPreferredSize(new Dimension(100, 50));
		this.btnSubmit.addActionListener(this);

		this.btnCancel = new JButton("Fermer");
		this.btnCancel.setName("btnCancel");
		this.btnCancel.setPreferredSize(new Dimension(100, 50));
		this.btnCancel.addActionListener(this);
		
		
		JPanel content = new JPanel();
		content.setLayout(new GridBagLayout());
		
		GridBagConstraints gbc = new GridBagConstraints();
		
		// Label 1
		gbc.insets = new Insets(3,  3,  3,  3);
		gbc.gridx = 0;
		gbc.gridy = 0;
		gbc.gridwidth = 1;
		gbc.gridheight = 1;
		content.add(lblPseudo, gbc);
		
		// Label 2
		gbc.gridy = 1;
		content.add(lblPassword, gbc);
		
		// Button
		gbc.insets = new Insets(10, 3, 10, 3);
		gbc.gridy = 2;
		gbc.fill = GridBagConstraints.HORIZONTAL;
		gbc.weightx = 1.0;
		content.add(btnCancel, gbc);
		
		// Button 2
		gbc.insets = new Insets(10, 3, 10, 3);
		gbc.gridx = 1;
		gbc.fill = GridBagConstraints.HORIZONTAL;
		gbc.weightx = 1.0;
		content.add(btnSubmit, gbc);
		
		// TextBox 1
		gbc.insets = new Insets(3,  3,  3,  3);
		gbc.gridy = 0;
		content.add(txtName, gbc);
		
		// TextBox 2
		gbc.gridy = 1;
		content.add(txtDesc, gbc);
		
		this.setContentPane(content);
	}
	
	/**
	 * Active ou désactive le contenu de la fenêtre
	 * @param active : Boolean qui active ou pas les éléments de la fenêtre
	 */
	private void setActiveIHM(boolean active) {
		txtName.setEnabled(active);
		txtDesc.setEnabled(active);
		btnSubmit.setEnabled(active);
	}
	
	/**
	 * Permet de se connecter via les identifiants
	 * @param user : Pseudo de l'utilisateur
	 * @param password : Mot de passe de l'utilisateur (En MD5 ou non)
	 * @param hash : Si le mot de passe est déjà en MD5
	 */
	private void addTask(String name, String desc) {
		
		if (name.equals("") || desc.equals("")) {
			setActiveIHM(true);
			return;
		}
		
		/**
		 * Requête pour insérer une tache
		 */
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("INSERT INTO task(t_name, t_desc, t_author, t_check) VALUES(?, ?, ?, ?)");
			
			prepare.setString(1, name);
			prepare.setString(2, desc);
			prepare.setString(3, this.account.getUserName());
			prepare.setInt(4, 0);
			
			prepare.executeUpdate();
			
			JOptionPane.showMessageDialog(null, "Ajout terminé !", "Ajout d'une tâche", JOptionPane.INFORMATION_MESSAGE);
			
		} catch (Exception e) {
			JOptionPane.showMessageDialog(null, "La connexion à la base de donnée a échoué !" + e.getMessage(), "Erreur de connexion", JOptionPane.ERROR_MESSAGE);
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
		
		setActiveIHM(true);
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
		if (arg0.getSource() instanceof JButton) {
			JButton btn = (JButton) arg0.getSource();
			
			if (btn.getName().equals("btnSubmit")) {
				// Ajout ...
				String user = txtName.getText();
				String pass = txtDesc.getText();
				
				txtName.setText("");
				txtDesc.setText("");
				
				setActiveIHM(false);
				
				this.addTask(user, pass);
			}
			
			if (btn.getName().equals("btnCancel")) {
				// Fermer ...
				this.setVisible(false);
			}
		}
	}
	
}
