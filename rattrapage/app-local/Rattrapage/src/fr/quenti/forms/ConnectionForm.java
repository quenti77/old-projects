package fr.quenti.forms;

import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.ResultSet;
import java.sql.SQLException;

import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;

import com.mysql.jdbc.PreparedStatement;

import fr.quenti.tools.Security;
import fr.quenti.tools.SingleBDD;
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
	
	private JTextField txtPseudo = null;
	private JPasswordField txtPassword = null;
	private JButton btnRegister = null;
	private JButton btnSubmit = null;
	
	/**
	 * Constructeur de la classe ConnectionForm
	 */
	public ConnectionForm() {
		this.createIHM();
		this.setSize(new Dimension(400, 150));
		this.setResizable(false);
		
		this.setTitle("Connexion ...");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	}
	
	/**
	 * Méthode de création de la fenêtre
	 */
	private void createIHM() {
		JLabel lblPseudo = new JLabel("Votre pseudo : ");
		JLabel lblPassword = new JLabel("Votre mot de passe : ");
		
		this.txtPseudo = new JTextField();
		this.txtPseudo.setPreferredSize(new Dimension(200, 50));
		
		this.txtPassword = new JPasswordField();
		this.txtPassword.setPreferredSize(new Dimension(200, 50));
		
		this.btnSubmit = new JButton("Se connecter ...");
		this.btnSubmit.setName("btnSubmit");
		this.btnSubmit.setPreferredSize(new Dimension(100, 50));
		this.btnSubmit.addActionListener(this);

		this.btnRegister = new JButton("S'inscrire ...");
		this.btnRegister.setName("btnRegister");
		this.btnRegister.setPreferredSize(new Dimension(100, 50));
		this.btnRegister.addActionListener(this);
		
		
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
		content.add(btnRegister, gbc);
		
		// Button 2
		gbc.insets = new Insets(10, 3, 10, 3);
		gbc.gridx = 1;
		gbc.fill = GridBagConstraints.HORIZONTAL;
		gbc.weightx = 1.0;
		content.add(btnSubmit, gbc);
		
		// TextBox 1
		gbc.insets = new Insets(3,  3,  3,  3);
		gbc.gridy = 0;
		content.add(txtPseudo, gbc);
		
		// TextBox 2
		gbc.gridy = 1;
		content.add(txtPassword, gbc);
		
		this.setContentPane(content);
	}
	
	/**
	 * Active ou désactive le contenu de la fenêtre
	 * @param active : Boolean qui active ou pas les éléments de la fenêtre
	 */
	private void setActiveIHM(boolean active) {
		txtPseudo.setEnabled(active);
		txtPassword.setEnabled(active);
		btnSubmit.setEnabled(active);
	}
	
	/**
	 * Permet de se connecter
	 * @param user :  Nom de l'utilisateur
	 * @param password : Mot de passe non crypté
	 */
	private void connection(String user, String password) {
		this.connection(user, password, false);
	}
	
	/**
	 * Permet de se connecter via les identifiants
	 * @param user : Pseudo de l'utilisateur
	 * @param password : Mot de passe de l'utilisateur (En MD5 ou non)
	 * @param hash : Si le mot de passe est déjà en MD5
	 */
	private void connection(String user, String password, boolean hash) {
		
		if (user.equals("") || password.equals("")) {
			setActiveIHM(true);
			return;
		}

		if (!hash) {
			password = Security.encode(password);
		}
		
		/**
		 * Requête pour savoir si l'utilisateur existe
		 * et permet de récupérer ses informations.
		 */
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("SELECT u_id, u_pseudo, u_mail FROM users WHERE u_pseudo = ? AND u_passwd = ?");
			
			prepare.setString(1, user);
			prepare.setString(2, password);
			
			ResultSet result = prepare.executeQuery();

			if (result.next()) {
				// Appel du formulaire UpdateConfirmForm
				Account account = new Account(result.getInt("u_id"), result.getString("u_pseudo"), result.getString("u_mail"));
				
				UpdateConfirmForm frmUpdateConfirm = new UpdateConfirmForm(account, this);
				frmUpdateConfirm.setVisible(true);
				this.setVisible(false);
			} else {
				JOptionPane.showMessageDialog(null, "Votre pseudo ou votre mot de passe est incorrect", "Erreur d'authentification", JOptionPane.ERROR_MESSAGE);
			}
			
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
	
	/**
	 * Relance l'application en se reconnectant directement
	 * ou non si la personne à décider de se déconnecter.
	 * @param account
	 */
	public void reload(Account account) {
		this.setVisible(true);
		
		if (account != null) {
			PreparedStatement prepare = null;
			try {
				prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("SELECT u_id, u_pseudo, u_passwd FROM users WHERE u_id = ?");
				
				prepare.setInt(1, account.getId());
				
				ResultSet result = prepare.executeQuery();

				if (result.next()) {
					connection(result.getString("u_pseudo"), result.getString("u_passwd"), true);
				} else {
					JOptionPane.showMessageDialog(null, "Votre pseudo ou votre mot de passe est incorrect", "Erreur d'authentification", JOptionPane.ERROR_MESSAGE);
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
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
		if (arg0.getSource() instanceof JButton) {
			JButton btn = (JButton) arg0.getSource();
			
			if (btn.getName().equals("btnSubmit")) {
				// Connexion ...
				String user = txtPseudo.getText();
				String pass = new String(txtPassword.getPassword());
				
				txtPseudo.setText("");
				txtPassword.setText("");
				
				setActiveIHM(false);
				
				this.connection(user, pass);
			}
			
			if (btn.getName().equals("btnRegister")) {
				// Inscription ...
				InscriptionForm frmInscription = new InscriptionForm(this);
				frmInscription.setVisible(true);
				this.setVisible(false);
			}
		}
	}
	
}
