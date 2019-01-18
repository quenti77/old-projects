package fr.directory.plugin;

import java.io.File;
import java.util.ArrayList;

import javax.swing.table.AbstractTableModel;

public class FileListModel extends AbstractTableModel {
	
	private static final long serialVersionUID = -3100007930388638960L;
	private ArrayList<File> files = new ArrayList<File>();
	private String[] head = {"Nom", "Chemin", "Taille", "Dossier ?"};

	@Override
	public int getColumnCount() {
		return head.length;
	}

	@Override
	public int getRowCount() {
		return files.size();
	}
	
	@Override
	public String getColumnName(int arg0) {
		return head[arg0];
	}

	@Override
	public Object getValueAt(int arg0, int arg1) {
		switch (arg1) {
		case 0:
			return files.get(arg0).getName();
		case 1:
			return files.get(arg0).getParentFile().getPath();
		case 2:
			if (files.get(arg0).isDirectory()) {
				return "0";
			} else {
				long l = files.get(arg0).length();
				String taille = "O";
				
				if (l > 1024) { l = (long) (l / 1024.0); taille = "Ko"; }
				if (l > 1024) { l = (long) (l / 1024.0); taille = "Mo"; }
				if (l > 1024) { l = (long) (l / 1024.0); taille = "Go"; }
				if (l > 1024) { l = (long) (l / 1024.0); taille = "To"; }
				
				return "" + l + " " + taille;
			}
		case 3:
			return files.get(arg0).isDirectory();
		default:
			return "";
		}
	}
	
	public void addFile(File file) {
		files.add(file);
		
		fireTableRowsInserted(files.size() - 1, files.size() - 1);
	}
	
	public void removeFile(int file) {
		files.remove(file);
		
		fireTableRowsDeleted(file, file);
	}

}
