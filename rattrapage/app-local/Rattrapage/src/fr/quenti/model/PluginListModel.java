package fr.quenti.model;

import java.util.ArrayList;

import javax.swing.table.AbstractTableModel;

/**
 * Représente un model de table
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class PluginListModel extends AbstractTableModel {

	private static final long serialVersionUID = 6886711232236856478L;

	private final ArrayList<Plugin> plugins = new ArrayList<Plugin>();
	private final String[] head = {"id", "nom", "version", "action"};
	
	public PluginListModel() {
		super();
	}
	
	public String getColumnName(int columnIndex) {
        return head[columnIndex];
    }
	
	@Override
	public int getColumnCount() {
		return head.length;
	}

	@Override
	public int getRowCount() {
		return plugins.size();
	}

	@Override
	public Object getValueAt(int arg0, int arg1) {
		switch(arg1) {
		case 0:
			return plugins.get(arg0).getId();
		case 1:
			return plugins.get(arg0).getName();
		case 2:
			return plugins.get(arg0).getVersion();
		case 3:
			int actionResult = plugins.get(arg0).getAction();
			
			switch (actionResult) {
			case 0:
				return"Vérification";
			case 1:
				return"Mise à jour";
			case 2:
				return"Suppression";
			case 4:
				return"Installation";
			default:
				return"Inconnue";
			}
		default:
			return null;
		}
	}
	
	public void addPlugins(Plugin plugin) {
		plugins.add(plugin);
		
		fireTableRowsInserted(plugins.size() - 1, plugins.size() - 1);
	}
	
	public void removePlugins(int rowIndex) {
		plugins.remove(rowIndex);
		
		fireTableRowsDeleted(rowIndex, rowIndex);
	}

	public Plugin getPlugin(int rowIndex) {
		return plugins.get(rowIndex);
	}
	
}
