let scene, camera, renderer, mixer;
// Declare controls globally so it can be updated in the animate function
let controls; 

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
    
    // --- FIX: Initialize Controls Here ---
    // The OrbitControls constructor is now available via the global THREE object
    controls = new THREE.OrbitControls(camera, renderer.domElement); 
    controls.enableDamping = true; // Enable damping for a smoother, more professional feel
    controls.dampingFactor = 0.05;

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

//do actual model loading
function loadModel() {
    // GLTFLoader is now available on the global THREE object
    const loader = new THREE.GLTFLoader(); 
    
    // Replace 'model.glb' with the path to your 3D model file
    loader.load('testModel.glb', function (gltf) {
        const model = gltf.scene;
        scene.add(model);

        // --- Animation Setup ---
        
        // 1. Create the AnimationMixer
        mixer = new THREE.AnimationMixer(model);

        // 2. Find an animation clip (gltf.animations is an array)
        if (gltf.animations.length > 0) {
            const clip = gltf.animations[0]; 
            
            // 3. Create an action for the clip
            const action = mixer.clipAction(clip);
            
            // Optional: Loop the animation
            action.loop = THREE.LoopRepeat; 
            
            // 4. Play the animation
            action.play();
        } else {
            console.warn("Model contains no animations.");
        }

        // Optional: Ensure the model casts shadows
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

//play an anim on the obj
function animate() {
    requestAnimationFrame(animate);

    const delta = clock.getDelta(); // Time elapsed since last frame
    
    // Update the mixer based on the elapsed time
    if (mixer) {
        mixer.update(delta);
    }
    
    // Update the controls (required if controls.enableDamping is true)
    if (controls) {
        controls.update(); 
    }

    // Render the scene
    renderer.render(scene, camera);
}


// --- EXECUTION: Start the application ---
init();
loadModel();
animate();