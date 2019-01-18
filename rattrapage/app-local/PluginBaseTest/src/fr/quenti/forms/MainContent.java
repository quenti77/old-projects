package fr.quenti.forms;

import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JLabel;
import javax.swing.JPanel;

import fr.quenti.pluginBaseTest.PluginBaseTest;

/**
 * Contenu principale du plugin
 * @author quenti77
 * @version 0.0.0.1-dev1
 */
public class MainContent extends JPanel implements ActionListener {

	/**
	 * Serial
	 */
	private static final long serialVersionUID = 11L;
	
	private PluginBaseTest plugin = null;
	
	public MainContent(PluginBaseTest plugin) {
		this.plugin = plugin;
		this.createIHM();
	}
	
	private void createIHM() {
		this.setLayout(new GridBagLayout());
		
		GridBagConstraints gbc = new GridBagConstraints();
		gbc.insets = new Insets(3,  3,  3,  3);
		gbc.gridx = 0;
		gbc.gridy = 0;
		gbc.gridwidth = 1;
		gbc.gridheight = 1;
		gbc.fill = GridBagConstraints.HORIZONTAL;
		
		JLabel lblId = new JLabel("ID : " + plugin.getAccount().getId());
		this.add(lblId, gbc);
		
		gbc.gridy = 1;
		JLabel lblPseudo = new JLabel("Pseudo : " + plugin.getAccount().getUserName());
		this.add(lblPseudo, gbc);
		
		gbc.gridy = 2;
		JLabel lblMail = new JLabel("Mail : " + plugin.getAccount().getMail());
		this.add(lblMail, gbc);
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
	}
	
	
}
