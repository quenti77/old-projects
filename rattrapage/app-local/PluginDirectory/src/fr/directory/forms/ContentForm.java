package fr.directory.forms;

import java.awt.BorderLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Scanner;

import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTabbedPane;
import javax.swing.JTable;
import javax.swing.ListSelectionModel;

import fr.directory.plugin.BasePlugin;
import fr.directory.plugin.FileListModel;

public class ContentForm extends JPanel implements ActionListener, MouseListener {

	private static final long serialVersionUID = 10L;
	
	private JButton btnRefresh = null;
	private JTabbedPane tab = null;
	private BasePlugin basePlugin = null;
	
	private File configDir = null;
	private ArrayList<File> paths = null;
	private ArrayList<JTable> tables = null;
	
	/**
	 * Constructeur pour le composant.
	 * @param menu : La barre de menu.
	 */
	public ContentForm(JMenuBar menu, BasePlugin plugin) {
		
		this.basePlugin = plugin;
		
		if (paths == null) {
			paths = new ArrayList<File>();
		}
		
		if (tables == null) {
			tables = new ArrayList<JTable>();
		}
		
		// Menu : gestion
		JMenu mnuGestion = new JMenu("Onglets v2");
		mnuGestion.addActionListener(this);
		mnuGestion.setName("mnuTab");

		// Item gestion : Créer nouvel onglet
		JMenuItem mnuGestionCreate = new JMenuItem("Nouvel onglet");
		mnuGestionCreate.addActionListener(this);
		mnuGestionCreate.setName("mnuTabCreate");
		
		// Item gestion : Fermer
		JMenuItem mnuGestionRemove = new JMenuItem("Fermer l'onglet");
		mnuGestionRemove.addActionListener(this);
		mnuGestionRemove.setName("mnuTabRemove");
		
		// Item gestion : recharger
		JMenuItem mnuGestionRefresh = new JMenuItem("Recharger l'onglet");
		mnuGestionRefresh.addActionListener(this);
		mnuGestionRefresh.setName("mnuTabRefresh");
		
		// Item gestion : Parent dir
		JMenuItem mnuGestionParent = new JMenuItem("Dossier parent");
		mnuGestionParent.addActionListener(this);
		mnuGestionParent.setName("mnuTabParent");
		
		mnuGestion.add(mnuGestionCreate);
		mnuGestion.add(mnuGestionRemove);
		mnuGestion.add(mnuGestionRefresh);
		mnuGestion.add(mnuGestionParent);
		
		menu.add(mnuGestion);
		
		configDir = this.getConfigPath(this.basePlugin.getAccount().getUserName());
		
		if (!configDir.exists()) {
			try {
				configDir.mkdirs();
			} catch (SecurityException se) {
				JOptionPane.showMessageDialog(null, "Erreur lors de la création du dossier", "Erreur de création", JOptionPane.ERROR_MESSAGE);
			}
		}
		
		this.getConfig();
		
		this.createIHM();
	}
	
	private File getConfigPath(String username) {
		String path = System.getProperty("user.dir");
		File parent = new File(path);
		File pluginsDir = new File(parent.getAbsolutePath() + File.separator +  "configPluginDirectory" + File.separator + username + File.separator);
		return pluginsDir;
	}
	
	private void getConfig() {
		
		try {
			File f = new File(this.configDir.getAbsolutePath() + File.separator + "config.txt");
			
			if (!f.exists()) {
				f.createNewFile();
			}
			
			Scanner scanner = new Scanner(new File(this.configDir.getAbsolutePath() + File.separator + "config.txt"));
			
			while (scanner.hasNextLine()) {
				String line = scanner.nextLine();
				
				File temp = new File(line);
				
				if (temp.exists() && temp.isDirectory()) {
					paths.add(temp);
				}
			}
			
			scanner.close();
			
		} catch (FileNotFoundException e) {
			JOptionPane.showMessageDialog(null, "Le fichier n'existe pas", "Erreur de fichier", JOptionPane.ERROR_MESSAGE);
		} catch (IOException e) {
			JOptionPane.showMessageDialog(null, "Le fichier n'a pas pu être créé", "Erreur de fichier", JOptionPane.ERROR_MESSAGE);
		}
	}
	
	private void setConfig() {
		
		FileWriter out = null;
		BufferedWriter writer = null;
		try {
			out = new FileWriter(this.configDir.getAbsolutePath() + File.separator + "config.txt", false);
			writer = new BufferedWriter(out);
			
			for (File file : paths) {
				writer.write(file.getAbsolutePath() + System.getProperty("line.separator"));
			}
			
		} catch (IOException e) {
			
		} finally {
			if (writer != null) {
				try {
					writer.close();
				} catch (IOException e) {
				}
			}
			
			if (out != null) {
				try {
					out.close();
				} catch (IOException e) {
				}
			}
		}
	}
	
	private void addTab(File file) {
		
		FileListModel model = new FileListModel();
		
		for (File f : file.listFiles()) {
			model.addFile(f);
		}
		
		JTable table = new JTable(model);
		table.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
		table.addMouseListener(this);
		
		tables.add(table);
		
		JPanel panelMainTab = new JPanel(new BorderLayout());
		panelMainTab.add(new JScrollPane(table), BorderLayout.CENTER);
		
		tab.add(file.getName(), panelMainTab);
		tab.setToolTipTextAt(tab.getTabCount() - 1, file.getAbsolutePath());
	}

	/**
	 * Permet la création de l'interface à afficher
	 */
	private void createIHM() {
		
		// Bouton de rafraichissement
		btnRefresh = new JButton("Rafraichir l'onglet ...");
		btnRefresh.setName("btnRefresh");
		btnRefresh.addActionListener(this);
		
		// Tab des dossiers 
		tab = new JTabbedPane(JTabbedPane.NORTH);
		
		for (File file : paths) {
			addTab(file);
		}
		
		this.setLayout(new BorderLayout());
		
		this.add(tab, BorderLayout.CENTER);
		this.add(btnRefresh, BorderLayout.SOUTH);
	}
	
	private void addTabAction() {
		JFileChooser dir = new JFileChooser();
		
		dir.setCurrentDirectory(new java.io.File("."));
		dir.setDialogTitle("Choisir un dossier");
		dir.setFileSelectionMode( JFileChooser.DIRECTORIES_ONLY);
		dir.setAcceptAllFileFilterUsed(false);
		
		if (dir.showOpenDialog(this) == JFileChooser.APPROVE_OPTION) {
			
			File f = dir.getSelectedFile();

			if (f != null) {
				addTab(f);
				paths.add(f);
				this.setConfig();
			}
		}
	}
	
	private void refreshTab(int index) {
	
		if (index < 0) {
			return;
		}
		
		File f = paths.get(index);
		JTable t = tables.get(index);

		FileListModel model = new FileListModel();
		
		for (File fSelect : f.listFiles()) {
			model.addFile(fSelect);
		}
		
		t.setModel(model);
		tab.setTitleAt(index, f.getName());
	}

	@Override
	public void actionPerformed(ActionEvent e) {
		
		int selectTab = tab.getSelectedIndex();
		
		if (e.getSource() instanceof JButton) {
			
			JButton tmpButton = (JButton) e.getSource();
			
			if ("btnRefresh".equalsIgnoreCase(tmpButton.getName())) {
				refreshTab(selectTab);
			}
			
		} else if (e.getSource() instanceof JMenuItem) {
			
			JMenuItem tmpMenu = (JMenuItem) e.getSource();
			
			if ("mnuTabCreate".equalsIgnoreCase(tmpMenu.getName())) {
				addTabAction();
			}
			
			if ("mnuTabRefresh".equalsIgnoreCase(tmpMenu.getName())) {
				refreshTab(selectTab);
				setConfig();
			}
			
			if ("mnuTabRemove".equalsIgnoreCase(tmpMenu.getName())) {
				if (selectTab < 0) {
					return;
				}
				
				tab.remove(selectTab);
				tables.remove(selectTab);
				paths.remove(selectTab);
				setConfig();
			}
			
			if ("mnuTabParent".equalsIgnoreCase(tmpMenu.getName())) {
				
				if (selectTab < 0) {
					return;
				}
				
				File f = paths.get(selectTab);
				File nf = f.getParentFile();
				
				if (nf != null && nf.isDirectory()) {
					paths.remove(selectTab);
					paths.add(selectTab, nf);
					refreshTab(tab.getSelectedIndex());
					setConfig();
				}
			}
			
		}
	}

	public BasePlugin getBasePlugin() {
		return basePlugin;
	}

	@Override
	public void mouseClicked(MouseEvent arg0) {
		if (arg0.getClickCount() == 2) {
			if (arg0.getSource() instanceof JTable) {
				JTable t = (JTable) arg0.getSource();
				Integer select = t.getSelectedRow();
				File f = paths.get(tab.getSelectedIndex());
				File nf = new File(f.getAbsolutePath() + File.separator + t.getModel().getValueAt(select, 0));
				
				if (nf != null && nf.isDirectory()) {
					paths.set(tab.getSelectedIndex(), nf);
					refreshTab(tab.getSelectedIndex());
				}
			}
		}
	}

	@Override
	public void mouseEntered(MouseEvent arg0) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void mouseExited(MouseEvent arg0) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void mousePressed(MouseEvent arg0) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void mouseReleased(MouseEvent arg0) {
		// TODO Auto-generated method stub
		
	}
}
