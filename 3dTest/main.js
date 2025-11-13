import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

let scene, camera, renderer, mixer;
const clock = new THREE.Clock(); // Used for tracking time for the animation mixer

function init() {
    // 1. Scene
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xaaaaaa); // Light gray background

    // 2. Camera
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(0, 1, 3); // Adjust position to view the model

    // 3. Renderer
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.shadowMap.enabled = true; // Enable shadow maps
    document.body.appendChild(renderer.domElement);

    // 4. Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5); // Soft white light
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
    directionalLight.position.set(5, 10, 7.5);
    directionalLight.castShadow = true;
    scene.add(directionalLight);

    // Handle window resizing
    window.addEventListener('resize', onWindowResize, false);
}

function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}