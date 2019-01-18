package fr.quenti.forms;

import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

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

/**
 * Formulaire d'inscription
 * @author quenti77
 * @version 0.0.0.1-dev1
 */
public class InscriptionForm extends JFrame implements ActionListener {

	private static final long serialVersionUID = 7L;
	
	private JTextField txtPseudo = null;
	private JPasswordField txtPassword = null;
	private JPasswordField txtConfirm = null;
	private JTextField txtMail = null;
	
	private JButton btnSubmit = null;
	private JButton btnClear = null;
	private JButton btnReturn = null;
	
	private ConnectionForm connectForm = null;
	
	public InscriptionForm(ConnectionForm connectForm) {
		this.connectForm = connectForm;
		this.connectForm.setVisible(false);
		
		this.createIHM();
		this.setSize(new Dimension(500, 220));
		this.setResizable(false);
		
		this.setTitle("Inscription ...");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	}

	/**
	 * Création de l'interface d'inscription
	 */
	private void createIHM() {
		
		JPanel content = new JPanel();
		content.setLayout(new GridBagLayout());
		
		JLabel lblName = new JLabel("Votre pseudo : ");
		JLabel lblPass = new JLabel("Votre mot de passe : ");
		JLabel lblConf = new JLabel("Confirmation : ");
		JLabel lblMail = new JLabel("Votre email : ");
		
		this.txtPseudo = new JTextField();
		this.txtPseudo.setPreferredSize(new Dimension(200, 20));
		
		this.txtPassword = new JPasswordField();
		this.txtPassword.setPreferredSize(new Dimension(200, 20));
		
		this.txtConfirm = new JPasswordField();
		this.txtConfirm.setPreferredSize(new Dimension(200, 20));
		
		this.txtMail = new JTextField();
		this.txtMail.setPreferredSize(new Dimension(200, 20));
		
		this.btnSubmit = new JButton("S'inscrire ...");
		this.btnSubmit.setName("btnSubmit");
		this.btnSubmit.addActionListener(this);
		
		this.btnClear = new JButton("Effacer ...");
		this.btnClear.setName("btnClear");
		this.btnClear.addActionListener(this);
		
		this.btnReturn = new JButton("Revenir à la connexion ...");
		this.btnReturn.setName("btnReturn");
		this.btnReturn.addActionListener(this);
		
		GridBagConstraints gbc = new GridBagConstraints();
		
		// Label pour le nom
		gbc.insets = new Insets(3,  3,  3,  3);
		gbc.gridx = 0;
		gbc.gridy = 0;
		gbc.gridwidth = 1;
		gbc.gridheight = 1;
		gbc.fill = GridBagConstraints.HORIZONTAL;
		content.add(lblName, gbc);
		
		// Label pour le pass 1
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 1;
		content.add(lblPass, gbc);
		
		// Label pour le pass 2
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 2;
		content.add(lblConf, gbc);
		
		// Label pour le mail
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 3;
		content.add(lblMail, gbc);
		
		// Text pour le pseudo
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridx = 1;
		gbc.gridy = 0;
		content.add(txtPseudo, gbc);
		
		// Text pour le pass 1
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 1;
		content.add(txtPassword, gbc);
		
		// Text pour le pass 2
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 2;
		content.add(txtConfirm, gbc);
		
		// Text pour le mail
		gbc.insets = new Insets(3, 3, 3, 3);
		gbc.gridy = 3;
		content.add(txtMail, gbc);
		
		// Button submit
		gbc.insets = new Insets(10, 3, 3, 3);
		gbc.gridx = 0;
		gbc.gridy = 4;
		gbc.weightx = 1.0;
		content.add(btnSubmit, gbc);
		
		// Button clear
		gbc.insets = new Insets(10, 3, 3, 3);
		gbc.gridx = 1;
		gbc.gridy = 4;
		gbc.weightx = 1.0;
		content.add(btnClear, gbc);
		
		// Button return
		gbc.insets = new Insets(10, 3, 3, 3);
		gbc.gridx = 0;
		gbc.gridy = 5;
		gbc.gridwidth = 2;
		gbc.weightx = 1.0;
		content.add(btnReturn, gbc);
		
		this.setContentPane(content);
	}
	
	/**
	 * Ajoute un utilisateur à la base de données.
	 * @param user : Le pseudo de l'utilisateur
	 * @param pass : Le mot de passe MD5 de l'utiilisateur
	 * @param mail : Le mail de l'utilisateur
	 */
	private void addUser(String user, String pass, String mail) {
		/**
		 * Requête pour savoir si le pseudo ou le mail est pas déjà utilisé !
		 */
		PreparedStatement prepare = null;
		try {
			String requestStr = "INSERT INTO users(u_pseudo, u_passwd, u_mail, u_last, u_check)";
			requestStr += "VALUES(?, ?, ?, NOW(), ?)";
			
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement(requestStr);
			
			prepare.setString(1, user);
			prepare.setString(2, pass);
			prepare.setString(3, mail);
			prepare.setInt(4, 1);
			
			prepare.executeUpdate();
			
			JOptionPane.showMessageDialog(null, "Vous êtes inscrit(e) : " + user, "Inscription terminé !", JOptionPane.INFORMATION_MESSAGE);
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
	}
	
	/**
	 * Création d'un nouveau compte après plusieurs test
	 * @param user : Le pseudo de l'utilisateur
	 * @param pass : Le mot de passe de l'utilisateur
	 * @param conf : Le mot de passe confirmer de l'utilisateur
	 * @param mail : Le mail de l'utilisateur
	 */
	private void register(String user, String pass, String conf, String mail) {
		
		if (user.equals("") || pass.equals("") || conf.equals("") || mail.equals("") ) {
			JOptionPane.showMessageDialog(null, "Tous les champs ne sont pas remplis", "Erreur d'inscription", JOptionPane.ERROR_MESSAGE);
			setActiveIHM(true);
			return;
		}

		pass = Security.encode(pass);
		conf = Security.encode(conf);
		
		Pattern p = Pattern.compile("^[a-z0-9._-]+@[a-z0-9._-]{2,}\\.[a-z]{2,4}$");
		Matcher m = p.matcher(mail);
		
		if (!m.find()) {
			JOptionPane.showMessageDialog(null, "Le mail n'a pas une forme valide", "Erreur d'inscription", JOptionPane.ERROR_MESSAGE);
			setActiveIHM(true);
			return;
		}
		
		if (!pass.equalsIgnoreCase(conf)) {
			JOptionPane.showMessageDialog(null, "Les mots de passes ne sont pas identiques", "Erreur d'inscription", JOptionPane.ERROR_MESSAGE);
			setActiveIHM(true);
			return;
		}
		
		/**
		 * Requête pour savoir si le pseudo ou le mail est pas déjà utilisé !
		 */
		PreparedStatement prepare = null;
		try {
			prepare = (PreparedStatement) SingleBDD.getInstance().prepareStatement("SELECT u_id, u_pseudo, u_mail FROM users WHERE u_pseudo = ? OR u_mail = ?");
			
			prepare.setString(1, user);
			prepare.setString(2, mail);
			
			ResultSet result = prepare.executeQuery();

			// Quelqu'un existe déjà c'est pas bien
			if (result.next()) {
				JOptionPane.showMessageDialog(null, "Les mots de passes ne sont pas identiques", "Erreur d'inscription", JOptionPane.ERROR_MESSAGE);
				setActiveIHM(true);
				return;
			} else {
				this.addUser(user, pass, mail);
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
		
		this.setActiveIHM(true);
	}
	
	/**
	 * Rend disponible ou non l'interface de l'utilisateur
	 * @param active : Peut on agir dessus ?
	 */
	private void setActiveIHM(boolean active) {
		this.btnSubmit.setEnabled(active);
		this.btnClear.setEnabled(active);
		this.btnReturn.setEnabled(active);
		this.txtPseudo.setEnabled(active);
		this.txtPassword.setEnabled(active);
		this.txtConfirm.setEnabled(active);
		this.txtMail.setEnabled(active);
	}
	
	private void clear() {
		this.txtPseudo.setText("");
		this.txtPassword.setText("");
		this.txtConfirm.setText("");
		this.txtMail.setText("");
	}
	
	@Override
	public void actionPerformed(ActionEvent arg0) {
		
		if (arg0.getSource() instanceof JButton) {
			JButton btn = (JButton) arg0.getSource();
			
			if (btn.getName().equals("btnSubmit")) {
				// Inscription ...
				String user = txtPseudo.getText();
				String pass = new String(txtPassword.getPassword());
				String conf = new String(txtConfirm.getPassword());
				String mail = txtMail.getText();
				
				this.clear();
				
				setActiveIHM(false);
				this.register(user, pass, conf, mail);
			}
			
			if (btn.getName().equals("btnClear")) {
				// Clearing ...
				this.clear();
			}
			
			if (btn.getName().equals("btnReturn")) {
				// Return ...
				this.setVisible(false);
				this.connectForm.setVisible(true);
			}
		}
	}

}
