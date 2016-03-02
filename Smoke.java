package com.kony.vizfeatures.kl.testScripts;

import java.io.File;

import org.testng.annotations.AfterClass;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Parameters;
import org.testng.annotations.Test;

import com.kony.setup.ProjectManagement;
import com.kony.utils.ScreenshotManagement;
import com.kony.vizAutomation.kl.utils.KLObjects;
import com.kony.vizAutomation.testreporters.GeneralTestReportManager;
import com.kony.vizAutomation.utils.AutomationUtils;
import com.kony.vizAutomation.utils.FileUtils;
import com.kony.vizAutomation.utils.LocalMachineConfigManager;

public class Smoke {

	private static String pathToStoreSmokeRefShots = "testScripts"
			+ File.separator + "com" + File.separator + "kony" + File.separator
			+ "vizfeatures" + File.separator + "kl" + File.separator
			+ "referenceScreenshots" + File.separator + "smoke";
	private static String dirToStoreTestResults = "testScripts"
			+ File.separator + "com" + File.separator + "kony" + File.separator
			+ "vizfeatures" + File.separator + "kl" + File.separator
			+ "testResults";
	private static String pathToStoreFailedShots = dirToStoreTestResults
			+ File.separator + "failedScreenshots" + File.separator + "smoke";

	String masterId = "";
	String flexContainerId = "";
	private static GeneralTestReportManager reporter = null;
	private static Boolean isScreenshotMode = null;
	private ScreenshotManagement screenshotManager = new ScreenshotManagement(
			pathToStoreSmokeRefShots, pathToStoreFailedShots);

	@BeforeClass(alwaysRun = true)
	@Parameters("screenshotMode")
	public void setScreenshotMode(Boolean screenshotMode) {
		isScreenshotMode = screenshotMode;
	}

	@BeforeClass(alwaysRun = true)
	public void init() {
		reporter = new GeneralTestReportManager(dirToStoreTestResults,
				dirToStoreTestResults + File.separator + "smoke.xls");
		reporter.init();

		// Delete project from previous smoke test run
		String pathToSmokeProj = LocalMachineConfigManager
				.getProperty("VIZ_WORKSPACE") + "/kl";
		try {
			com.kony.vizAutomation.utils.FileUtils.delete(new File(
					pathToSmokeProj));
			Thread.sleep(3000);
		} catch (Exception e) {
			e.printStackTrace();
		}

		// Launch Viz and login
		AutomationUtils.initializeVisualizer(true);
		try {
			ProjectManagement.createNewProject();
			ProjectManagement.renameProject("kl");
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/*
	 * Flow : Create new library -> Verify directory in .userlibs -> Verify new
	 * name in KL
	 */
	@Test(groups = { "smokeM" }, priority = 1)
	public void createNewLib() throws Exception {
		String TEST_CASE_DESCRIPTION = "Create a new library";
		String errMsg = null;
		String libNames[] = { "testLib1", "testLib2" };
		String libPath = null;
		String currLibName = null;
		boolean testcaseStatus = true;

		// if library exists with above names delete them
		checkLibNames(libNames);

		// Create a new library with libName1
		KLObjects.createNewLibrary(libNames[0]);

		// Verify lib directory in .userlibs
		libPath = FileUtils.getPathToLibrary(libNames[0]);
		if (!FileUtils.isFilePresent(libPath, 300)) {
			testcaseStatus = false;
			errMsg = "Library is absent at given path";
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}

		// Verify library name in PX
		currLibName = KLObjects.getCurrentLibraryName();
		if (!currLibName.equals(libNames[0])) {
			testcaseStatus = false;
			errMsg = "Incorrect library name in kl -> Expected : "
					+ libNames[0] + " Present : " + currLibName;
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}

		if (testcaseStatus == true) {
			reporter.writeTestPassed(TEST_CASE_DESCRIPTION);
		}

	}

	/*
	 * Flow : rename library -> Verify File system -> Library name in KL section
	 */
	@Test(groups = { "smokeM" }, priority = 2, dependsOnMethods = { "createNewLib" })
	public void renameLib() throws Exception {
		String TEST_CASE_DESCRIPTION = "Rename the library";
		String errMsg = null;
		String libNames[] = { "testLib1", "testLib2" };
		String libPath = null;
		String currLibName = null;
		boolean testcaseStatus = true;

		// Rename the library name
		KLObjects.renameLibrary(libNames[0], libNames[1]);

		// Verify library directory in .userlibs
		// Verify with old library name -> must be absent
		libPath = FileUtils.getPathToLibrary(libNames[0]);
		if (FileUtils.isFilePresent(libPath, 300)) {
			testcaseStatus = false;
			errMsg = "Library with old name : " + libNames[0] + " is present";
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}

		// Verify with new library name -> must be present
		libPath = FileUtils.getPathToLibrary(libNames[1]);
		if (!FileUtils.isFilePresent(libPath, 300)) {
			testcaseStatus = false;
			errMsg = "Library with new name : " + libNames[1] + " is absent";
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}

		// Verify library name in PX
		currLibName = KLObjects.getCurrentLibraryName();
		if (!currLibName.equals(libNames[1])) {
			testcaseStatus = false;
			errMsg = "Incorrect library name in kl -> Expected : "
					+ libNames[1] + " Present : " + currLibName;
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}

		if (testcaseStatus == true) {
			reporter.writeTestPassed(TEST_CASE_DESCRIPTION);
		}

	}

	/*
	 * Flow : Delete library -> verify file system -> verify default library
	 * name in KL section
	 */
	@Test(groups = { "smokeM" }, priority = 3, dependsOnMethods = { "renameLib" })
	public void deleteLib() throws Exception {
		String TEST_CASE_DESCRIPTION = "Delete the library";
		String errMsg = null;
		String libNames[] = { "testLib1", "testLib2" };
		String defaultLibName = "KonyLibrary";
		String libPath = null;
		String currLibName = null;
		boolean testcaseStatus = true;

		// Delete the library name
		KLObjects.deleteLibrary(libNames[1]);

		// Verify library directory in .userlibs
		libPath = FileUtils.getPathToLibrary(libNames[1]);
		if (FileUtils.isFilePresent(libPath, 300)) {
			testcaseStatus = false;
			errMsg = "Library with name : " + libNames[1] + " is present";
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		} 

		// Verify library name in PX
		currLibName = KLObjects.getCurrentLibraryName();
		if (!currLibName.equals(defaultLibName)) {
			testcaseStatus = false;
			errMsg = "Incorrect library name in kl -> Expected : "
					+ defaultLibName + " Present : " + currLibName;
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		}
		
		if(testcaseStatus == true){
			reporter.writeTestPassed(TEST_CASE_DESCRIPTION);
		}
	}

	/*
	 * Flow : Create two libraries -> Open first library -> Verify library name
	 * in KL section
	 */
	@Test(groups = { "smokeM" }, priority = 4, dependsOnMethods = { "deleteLib" })
	public void openLib() throws Exception {
		String TEST_CASE_DESCRIPTION = "Open library";
		String errMsg = null;
		String currLibName = null;

		String libNames[] = { "testLib1", "testLib2" };

		// if library exists with above names delete them
		checkLibNames(libNames);

		// Create two libraries
		KLObjects.createNewLibrary(libNames[0]);
		KLObjects.createNewLibrary(libNames[1]);

		// Open first library
		KLObjects.openLibrary(libNames[0]);

		// Verify UI
		currLibName = KLObjects.getCurrentLibraryName();
		if (!currLibName.equals(libNames[0])) {
			errMsg = "Incorrect library name in kl -> Expected : "
					+ libNames[0] + " Present : " + currLibName;
																			
			reporter.writeTestFailed(TEST_CASE_DESCRIPTION, errMsg);
		} else {
			reporter.writeTestPassed(TEST_CASE_DESCRIPTION);
		}

	}

	/*
	 * Method checks libraries with given names and deletes libraries if they
	 * have same name
	 */
	private static void checkLibNames(String libNames[]) throws Exception {

		if (KLObjects.isLibraryExists(libNames[0])) {
			KLObjects.deleteLibrary(libNames[0]);
		}
		if (KLObjects.isLibraryExists(libNames[1])) {
			KLObjects.deleteLibrary(libNames[1]);
		}

	}

	@AfterClass(alwaysRun = true)
	public void closeReportManager() {
		reporter.close();
		AutomationUtils.closeVisualizer(); // close visualizer
	}

}
