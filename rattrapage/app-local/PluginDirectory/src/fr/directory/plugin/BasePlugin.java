package fr.directory.plugin;

import java.awt.Component;

import javax.swing.JMenuBar;

import fr.directory.forms.ContentForm;
import fr.quenti.plugins.IPlugin;
import fr.quenti.plugins.InformationPlugin;
import fr.quenti.users.Account;

public class BasePlugin implements IPlugin {

	private InformationPlugin informationPlugin = null;
	private Component content = null;
	private Account account = null;
	
	public Account getAccount() {
		return this.account;
	}
	
	@Override
	public InformationPlugin getInformation(Account account) {
		
		this.account = account;
		
		if ( informationPlugin == null ) {
			informationPlugin = new InformationPlugin("plug Directory testing", "plugDir", "0.0.0.1-dev1");
		}
		
		return informationPlugin;
	}

	@Override
	public Component getInterface(JMenuBar arg0) {
		
		if ( content == null ) {
			content = new ContentForm(arg0, this);
		}
		
		return content;
	}

}
