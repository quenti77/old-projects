package fr.plugin.data;

import java.util.ArrayList;

import javax.swing.table.AbstractTableModel;

public class TaskListModel extends AbstractTableModel {

	private static final long serialVersionUID = 4645434356L;
	
	private final ArrayList<Task> tasks = new ArrayList<Task>();
	private final String[] head = { "ID", "Nom", "Description", "Auteur", "Finis ?" };
	
	public TaskListModel() {
		super();
	}
	
	public String getColumnName(int columnIndex) {
		return head[columnIndex];
	}
	
	@Override
	public int getRowCount() {
		return tasks.size();
	}
	
	@Override
	public int getColumnCount() {
		return head.length;
	}
	
	@Override
	public Object getValueAt(int arg0, int arg1) {
		switch (arg1) {
		case 0:
			return tasks.get(arg0).getId();
		case 1:
			return tasks.get(arg0).getName();
		case 2:
			return tasks.get(arg0).getDesc();
		case 3:
			return tasks.get(arg0).getAuthor();
		case 4:
			return tasks.get(arg0).isChecked();
		default:
			return null;
		}
	}
	
	public void addTasks(Task task) {
		tasks.add(task);
		
		fireTableRowsInserted(tasks.size() - 1, tasks.size() - 1);
	}
	
	public void removeTasks(int id) {
		tasks.remove(id);
		
		fireTableRowsInserted(id, id);
	}
	
	public Task getPlugin(int rowIndex) {
		return tasks.get(rowIndex);
	}
}
