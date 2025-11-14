// main.js

// Using esm.sh to resolve internal module dependencies correctly in the browser.
// Note: The structure is similar to unpkg, but the internal dependency resolution is better.
import * as THREE from 'https://esm.sh/three@0.160.0';
import { GLTFLoader } from 'https://esm.sh/three@0.160.0/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'https://esm.sh/three@0.160.0/examples/jsm/controls/OrbitControls.js';


let scene, camera, renderer, mixer;
let controls; 

const clock = new THREE.Clock(); 

function init() {
    // 1. Scene
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xaaaaaa); 

    // 2. Camera
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(0, 1, 3); 

    // 3. Renderer
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.shadowMap.enabled = true; 
    document.body.appendChild(renderer.domElement);
    
    // Initialize Controls (accessed directly since it was imported as { OrbitControls })
    controls = new OrbitControls(camera, renderer.domElement); 
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.target.set(0,4,0); //set the target for the orbit camera

    // 4. Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5); 
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

function loadModel() {
    // GLTFLoader is accessed directly since it was imported as { GLTFLoader }
    const loader = new GLTFLoader(); 
    
    loader.load('testModel.glb', function (gltf) {
        const model = gltf.scene;
        scene.add(model);

        // --- Animation Setup ---
        mixer = new THREE.AnimationMixer(model);

        if (gltf.animations.length > 0) {
            const clip = gltf.animations[0]; 
            const action = mixer.clipAction(clip);
            action.loop = THREE.LoopRepeat; 
            action.play();
        } else {
            console.warn("Model contains no animations.");
        }

        model.traverse(function (child) {
            if (child.isMesh) {
                child.castShadow = true;
                child.receiveShadow = true;
            }
        });

    }, undefined, function (error) {
        console.error('An error happened loading the model:', error);
    });
}

function animate() {
    requestAnimationFrame(animate);

    const delta = clock.getDelta(); 
    
    if (mixer) {
        mixer.update(delta);
    }
    
    if (controls) {
        controls.update(); 
    }

    renderer.render(scene, camera);
}


// --- EXECUTION: Start the application ---
init();
loadModel();
animate();
