<script lang="ts">
    import { router } from '@inertiajs/svelte';

    function uploadFile(event: SubmitEvent) {
        event.preventDefault();

        if (!(event.currentTarget instanceof HTMLFormElement)) {
            return;
        }

        router.post('/', new FormData(event.currentTarget));
    }
</script>

<main>
    <h1>Automating my workflow</h1>
    <section>
        <h2>Upload Transaction History</h2>
        <form on:submit={uploadFile}>
            <input
                type="file"
                name="file"
                accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            >
            <button type="submit">Upload</button>
        </form>
    </section>
</main>

<style>
    input{
        border: 1px solid red;
    }

    section{
        border: 1px solid blueviolet;
    }
</style>