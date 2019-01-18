package fr.quenti.pluginBaseTest;

import java.awt.Component;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;

import fr.quenti.forms.MainContent;
import fr.quenti.plugins.IPlugin;
import fr.quenti.plugins.InformationPlugin;
import fr.quenti.users.Account;

/**
 * Plugin de base qui permet de tester l'interface
 * @author quenti77
 * @version 0.0.0.1-dev1
 */
public class PluginBaseTest implements IPlugin, ActionListener {

	private InformationPlugin informationPlugin = null;
	private Component content = null;
	private Account account = null;
	
	public Account getAccount() {
		return this.account;
	}
	
	@Override
	public InformationPlugin getInformation(Account account) {
		
		this.account = account;
		
		if (informationPlugin == null) {
			informationPlugin = new InformationPlugin("Plugin Base Test", "quenti77", "0.0.0.1");
		}
		
		return informationPlugin;
	}

	@Override
	public Component getInterface(JMenuBar arg0) {
		
		JMenu mnuInfo = new JMenu("Information Auteur");
		mnuInfo.setName("mnuInfo");
		
		JMenuItem mnuInfoAuteur = new JMenuItem("Auteur du plugin");
		mnuInfoAuteur.setName("mnuInfoAuteur");
		mnuInfoAuteur.addActionListener(this);
		
		mnuInfo.add(mnuInfoAuteur);
		arg0.add(mnuInfo);
		
		if (content == null) {
			content = new MainContent(this);
		}
		
		return content;
	}

	@Override
	public void actionPerformed(ActionEvent arg0) {
		
		if (arg0.getSource() instanceof JMenuItem) {
			JMenuItem tmpMenu = (JMenuItem) arg0.getSource();
			
			if ("mnuInfoAuteur".equalsIgnoreCase(tmpMenu.getName())) {
				JOptionPane.showMessageDialog(null, this.informationPlugin, "Information plugin", JOptionPane.INFORMATION_MESSAGE);
			}
		}
		
	}

}
