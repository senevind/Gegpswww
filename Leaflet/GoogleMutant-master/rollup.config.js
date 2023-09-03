// import resolve from "rollup-plugin-node-resolve";
// import commonjs from 'rollup-plugin-commonjs';
// import builtins from 'rollup-plugin-node-builtins';
// import globals from 'rollup-plugin-node-globals';
import buble from "@rollup/plugin-buble";
import pkg from "./package.json";

export default [
	// browser-friendly IIFE build
	{
		input: pkg.module,
		output: {
			file: pkg.browser,
			format: "iife",
			sourcemap: true,
			intro: `/**
 * GoogleMutant by Iván Sánchez Ortega <ivan@sanchezortega.es> https://ivan.sanchezortega.es
 * Source and issue tracking: https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant/
 *
 * Based on techniques from https://github.com/shramov/leaflet-plugins
 * and https://avinmathew.com/leaflet-and-google-maps/ , but relying on MutationObserver.
 *
 * "THE BEER-WARE LICENSE":
 * <ivan@sanchezortega.es> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return.
 *
 * Uses MIT-licensed code from https://github.com/rsms/js-lru/
 */`,
		},
		plugins: [
			// 			resolve(),
			buble(),
		],

		external: ["fs", "path"],
	},
];
