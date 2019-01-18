package fr.quenti.forms;

import java.awt.BorderLayout;
import java.awt.Dimension;

import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JProgressBar;
import javax.swing.SwingUtilities;

import fr.quenti.model.PluginListModel;
import fr.quenti.thread.ThreadUpdate;
import fr.quenti.users.Account;

/**
 * 
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class UpdateForm extends JFrame {

	private static final long serialVersionUID = 4L;
	
	private Account account = null;
	private ConnectionForm connect = null;
	private PluginListModel modelTable = null;
	private ThreadUpdate t = null;
	
	private JLabel lblStatus = null;
	private JProgressBar pbStatus = null;

	public UpdateForm(Account account, ConnectionForm connectForm, PluginListModel modelTable) {
		this.account = account;
		this.connect = connectForm;
		this.modelTable = modelTable;
		
		this.createIHM();
		this.pack();
		this.setMinimumSize(this.getSize());
		
		this.setTitle("Confirmation ...");
		this.setLocationRelativeTo(null);
		this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		
		this.t =  new ThreadUpdate(this, modelTable, this.account);
		this.t.setName("MAJ des plugins");
		this.t.start();
	}
	
	public Account getAccount() {
		return account;
	}

	public ConnectionForm getConnect() {
		return connect;
	}

	public PluginListModel getModelTable() {
		return modelTable;
	}

	/**
	 * Méthode appelé par le processus pour mettre
	 * à jour les informations de la fenêtre.
	 * @param status
	 * @param value
	 */
	public void setStatus(String status, int value) {
		
		if (value < 0) {
			// Fin
			JOptionPane.showMessageDialog(null, "Chargement terminé !", "Fin", JOptionPane.INFORMATION_MESSAGE);
			this.setVisible(false);
			
			SwingUtilities.invokeLater(new Runnable() {
				
				@Override
				public void run() {
					MainForm frmMain = new MainForm(account, connect, modelTable);
					frmMain.setVisible(true);
				}
			});
		} else {
			lblStatus.setText(status);
		
			pbStatus.setValue(value);
			pbStatus.setString("" + value + " %");
		}
	}
	
	/**
	 * Création de l'interface pour l'utilisateur
	 */
	private void createIHM() {
		
		JPanel content = new JPanel(new BorderLayout());
		
		// Label status
		lblStatus = new JLabel("Chargement de la liste");
		lblStatus.setName("lblStatus");
		
		// Progress bar
		pbStatus = new JProgressBar(JProgressBar.HORIZONTAL, 0, 100);
		pbStatus.setName("pbStatus");
		pbStatus.setValue(0);
		pbStatus.setStringPainted(true);
		pbStatus.setString("0 %");
		pbStatus.setPreferredSize(new Dimension(250, 35));
		
		content.add(lblStatus, BorderLayout.NORTH);
		content.add(pbStatus, BorderLayout.CENTER);
		
		this.getContentPane().add(content);
	}
	
}
