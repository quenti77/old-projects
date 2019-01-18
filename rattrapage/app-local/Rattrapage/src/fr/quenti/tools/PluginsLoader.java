package fr.quenti.tools;

import java.io.File;
import java.net.URL;
import java.net.URLClassLoader;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.jar.JarFile;

import javax.swing.JOptionPane;

import fr.quenti.plugins.IPlugin;

/**
 * Le chargeur de plugin
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class PluginsLoader {

	private ArrayList<File> paths = null;
	private ArrayList<IPlugin> classes = null;
	
	public PluginsLoader() throws Exception {
		
	}
	
	/**
	 * Créé une nouvelle instance de chaque plugin
	 * @throws Exception : Problème lors du chargement du plugin
	 */
	private void loadPlugins() throws Exception {
		URLClassLoader loader;
		Enumeration<?> enumeration;
		Class<?> classe = null;
		String temp = "";
		
		for (File file : this.paths) {
			if (!file.exists()) {
				throw new Exception("Un fichier de plugin inexistant");
			}

			URL url = file.toURI().toURL();
			loader = new URLClassLoader(new URL[] {url});
			
			JarFile jar = new JarFile(file.getAbsolutePath());
			enumeration = jar.entries();
			
			while (enumeration.hasMoreElements()) {
				temp = enumeration.nextElement().toString();
				
				if (temp.length() > 6 && temp.substring(temp.length() - 6).compareTo(".class") == 0) {
					temp = temp.substring(0, temp.length() -6);
					temp = temp.replaceAll("/", ".");
					
					classe = Class.forName(temp, true, loader);
					
					for (Class<?> c : classe.getInterfaces()) {
						try {
						if (c.toString().equals("interface fr.quenti.plugins.IPlugin")) {
							Object pluginObject = classe.newInstance();
							
							IPlugin plugin = (IPlugin) pluginObject;
							classes.add(plugin);
						}
						} catch (ClassCastException e) {
							JOptionPane.showMessageDialog(null, "Erreur de chargement des plugins !\n" + e.getMessage(), "Fin", JOptionPane.ERROR_MESSAGE);
						}
					}
				}
			}

			loader.close();
			jar.close();
		}
	}
	
	/**
	 * Permet la récupération des plugins
	 * @param username : le nom de l'utilisateur
	 * @return une liste des instances de IPlugin
	 * @throws Exception : Problème lors du chargement des plugins
	 */
	public ArrayList<IPlugin> getPlugins(String username) throws Exception {
		
		if (this.paths == null) {
			this.paths = new ArrayList<File>();
		}
		
		if (this.paths.size() == 0) {
			this.initPaths(username);
		}
		
		if (this.classes == null) {
			this.classes = new ArrayList<IPlugin>();
			this.loadPlugins();
		}
		
		return this.classes;
	}
	
	/**
	 * Permet de récupérer le chemin d'accès au dossier %APPLICATION_EXECUTE%/plugins/%USERNAME%/
	 * @param username : Le nom de l'utilisateur qui c'est connecté.
	 * @return un objet de type File.
	 */
	public static File getPluginsPath(String username) {
		String path = System.getProperty("user.dir");
		File parent = new File(path);
		File pluginsDir = new File(parent.getAbsolutePath() + File.separator +  "plugins" + File.separator + username + File.separator);
		
		return pluginsDir;
	}
	
	/**
	 * Initialise le dossier plugins de l'utilisateur
	 * @param username : Le nom de l'utilisateur qui c'est connecté.
	 * @throws SecurityException : Problème lors du chargement des plugins
	 */
	private void initPaths(String username) throws SecurityException {
		File pluginsDir = PluginsLoader.getPluginsPath(username);
		
		if (!pluginsDir.exists()) {
			boolean result = false;
			try {
				result = pluginsDir.mkdir();
			} catch (SecurityException se) {
				throw new SecurityException();
			}
			
			if (!result) return;
		}
		
		File[] pluginsFile = pluginsDir.listFiles();
		for (File file : pluginsFile) {
			String fileName = file.getName();
			Integer index = fileName.lastIndexOf(".");
			
			if (index != -1 && index != 0) {
				if (fileName.substring(index + 1).equalsIgnoreCase("jar")) {
					this.paths.add(file);
				}
			}
		}
	}
}
