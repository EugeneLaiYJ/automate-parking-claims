<script lang="ts">
    import { router } from '@inertiajs/svelte';

    type TransactionRow = {
        transaction_id: number;
        date_time: string;
        type: string;
        sector: string;
        entry_location: string;
        exit_location: string;
        amount: number;
    };

    let { transactions = [] }: { transactions?: TransactionRow[] } = $props();

    function downloadClaim(event: SubmitEvent) {
        event.preventDefault();

        if (!(event.currentTarget instanceof HTMLFormElement)) {
            return;
        }

        const formData = new FormData(event.currentTarget);
        const month = formData.get('month');

        if (typeof month !== 'string') {
            return;
        }

        window.location.href = `/claim/download?month=${encodeURIComponent(month)}`;
    }

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
        <form onsubmit={uploadFile}>
            <input
                type="file"
                name="file"
                accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            >
            <button type="submit">Upload</button>
        </form>
    </section>
    <section>
        <h2>Generate Claim for MONTH:</h2>
        <form onsubmit={downloadClaim}>
            <select id="month" name="month" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
            <button type="submit">Generate</button>
        </form>
        
    </section>
    <section>
        <h2>Transactions</h2>
        {#if transactions.length > 0}
            <div class="table-container">
                <table>
                    <caption>Transactions saved in the database</caption>
                    <thead>
                        <tr>
                            <th scope="col">Transaction ID</th>
                            <th scope="col">Date/Time</th>
                            <th scope="col">Type</th>
                            <th scope="col">Sector</th>
                            <th scope="col">Entry Location</th>
                            <th scope="col">Exit Location</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each transactions as transaction (transaction.transaction_id)}
                            <tr>
                                <td>{transaction.transaction_id}</td>
                                <td>{transaction.date_time}</td>
                                <td>{transaction.type}</td>
                                <td>{transaction.sector}</td>
                                <td>{transaction.entry_location}</td>
                                <td>{transaction.exit_location}</td>
                                <td>{transaction.amount}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {:else}
            <p>No transactions have been imported yet.</p>
        {/if}
    </section>
</main>

<style>
    input {
        border: 1px solid #9ca3af;
    }

    section {
        margin-block: 1.5rem;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    caption {
        padding-block: 0.5rem;
        text-align: left;
    }

    th,
    td {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        white-space: nowrap;
    }

    th {
        background: #f3f4f6;
    }
</style>